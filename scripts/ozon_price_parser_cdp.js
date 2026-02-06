#!/usr/bin/env node
/**
 * Ozon Price Parser - Connect to existing browser
 * 
 * Connects to an already running Chrome browser with remote debugging enabled.
 * This bypasses antibot detection because the browser is already trusted.
 * 
 * To use:
 * 1. Start Chrome with --remote-debugging-port=9222
 * 2. Navigate to Ozon and solve any captchas manually
 * 3. Run this script to extract prices
 * 
 * Usage: node ozon_price_parser_cdp.js <product_url_or_id>
 */

import { chromium } from 'playwright';

const CDP_PORT = process.env.CDP_PORT || 9222;

async function extractProductId(urlOrId) {
    if (/^\d+$/.test(urlOrId)) return urlOrId;
    const match = urlOrId.match(/\/product\/[^\/]*?(\d+)\/?/);
    if (match) return match[1];
    const numMatch = urlOrId.match(/(\d+)\/?$/);
    return numMatch ? numMatch[1] : null;
}

async function fetchPriceData(productId) {
    const debugInfo = [];
    let browser;

    try {
        // Try to connect to existing browser
        debugInfo.push(`Connecting to Chrome at port ${CDP_PORT}...`);
        browser = await chromium.connectOverCDP(`http://127.0.0.1:${CDP_PORT}`);
        debugInfo.push('Connected to existing browser');

    } catch (err) {
        return {
            error: `Cannot connect to Chrome. Please start Chrome with: google-chrome --remote-debugging-port=${CDP_PORT}`,
            success: false,
            debug: debugInfo
        };
    }

    try {
        // Get or create a context
        const contexts = browser.contexts();
        const context = contexts[0] || await browser.newContext();

        // Create new page
        const page = await context.newPage();

        const url = `https://www.ozon.ru/product/${productId}/`;
        debugInfo.push(`Navigating to: ${url}`);

        await page.goto(url, { waitUntil: 'networkidle', timeout: 60000 });

        // Wait for page load
        await page.waitForTimeout(3000);

        const title = await page.title();
        debugInfo.push(`Page title: ${title}`);

        // Check if blocked
        if (title.includes('Antibot') || title.includes('Доступ')) {
            debugInfo.push('Page blocked. Please solve captcha in browser and retry.');
            await page.close();
            return { error: 'Page blocked by antibot', success: false, debug: debugInfo };
        }

        // Check NUXT
        const hasNuxt = await page.evaluate(() => !!window.__NUXT__);
        debugInfo.push(`Has __NUXT__: ${hasNuxt}`);

        // Extract prices
        const priceData = await page.evaluate(() => {
            try {
                const nuxt = window.__NUXT__;
                if (nuxt) {
                    let ws = nuxt.state?.widgetStates || nuxt.widgetStates;
                    if (ws) {
                        const priceKey = Object.keys(ws).find(k => k.startsWith('webPrice-'));
                        if (priceKey) {
                            const p = JSON.parse(ws[priceKey]);
                            return { source: 'nuxt', cardPrice: p.cardPrice, price: p.price, originalPrice: p.originalPrice, isAvailable: p.isAvailable };
                        }
                    }
                }
            } catch (e) { }

            try {
                for (const s of document.querySelectorAll('script[type="application/ld+json"]')) {
                    const d = JSON.parse(s.textContent);
                    if (d.offers?.price) return { source: 'json_ld', price: d.offers.price + ' ₽', isAvailable: true };
                }
            } catch (e) { }

            try {
                const ps = document.body.textContent.match(/(\d{1,3}(?:\s?\d{3})*)\s*₽/g);
                if (ps?.length) return { source: 'dom', price: ps[0], allPrices: ps.slice(0, 10) };
            } catch (e) { }

            return null;
        });

        const sellerData = await page.evaluate(() => {
            try {
                const l = document.querySelector('a[href*="/seller/"]');
                if (l) return { name: l.textContent?.trim(), externalId: l.href.match(/\/seller\/(\d+)/)?.[1] };
            } catch (e) { }
            return null;
        });

        await page.close();

        if (!priceData) {
            return { error: 'Could not extract price data', success: false, debug: debugInfo };
        }

        const parsePrice = (s) => s ? parseFloat(s.replace(/[^\d.,]/g, '').replace(',', '.')) || null : null;

        return {
            success: true,
            productId,
            userPrice: parsePrice(priceData.cardPrice) || parsePrice(priceData.price),
            basePrice: parsePrice(priceData.price),
            originalPrice: parsePrice(priceData.originalPrice),
            isAvailable: priceData.isAvailable,
            seller: sellerData,
            source: priceData.source,
            raw: priceData,
            debug: debugInfo
        };

    } catch (error) {
        return { error: error.message, success: false, debug: debugInfo };
    }
}

const arg = process.argv[2];
if (!arg) {
    console.log(JSON.stringify({ error: 'Usage: node ozon_price_parser_cdp.js <product_url_or_id>', success: false }));
    process.exit(1);
}

const productId = await extractProductId(arg);
if (!productId) {
    console.log(JSON.stringify({ error: 'Could not extract product ID', success: false }));
    process.exit(1);
}

const result = await fetchPriceData(productId);
console.log(JSON.stringify(result, null, 2));
