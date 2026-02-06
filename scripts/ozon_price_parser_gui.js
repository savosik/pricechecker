#!/usr/bin/env node
/**
 * Ozon Price Parser - Non-headless mode
 * 
 * Uses visible browser window which may bypass antibot detection better
 */

import { chromium } from 'playwright';

async function extractProductId(urlOrId) {
    if (/^\d+$/.test(urlOrId)) return urlOrId;
    const match = urlOrId.match(/\/product\/[^\/]*?(\d+)\/?/);
    if (match) return match[1];
    const numMatch = urlOrId.match(/(\d+)\/?$/);
    if (numMatch) return numMatch[1];
    return null;
}

async function fetchPriceData(productId) {
    // Launch in non-headless mode
    const browser = await chromium.launch({
        headless: false,
        args: [
            '--no-sandbox',
            '--disable-blink-features=AutomationControlled'
        ]
    });

    const debugInfo = [];

    try {
        const context = await browser.newContext({
            userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            viewport: { width: 1920, height: 1080 },
            locale: 'ru-RU',
            timezoneId: 'Europe/Moscow'
        });

        await context.addInitScript(() => {
            Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
        });

        const page = await context.newPage();

        const url = `https://www.ozon.ru/product/${productId}/`;
        debugInfo.push(`Navigating to: ${url}`);

        await page.goto(url, { waitUntil: 'networkidle', timeout: 60000 });

        let title = await page.title();
        debugInfo.push(`Page title: ${title}`);

        // Wait for possible antibot resolution
        if (title.includes('Antibot') || title.includes('Challenge') || title.includes('Доступ')) {
            debugInfo.push('Antibot detected, waiting for manual resolution...');
            // Wait up to 30 seconds for user to solve
            await page.waitForFunction(
                () => !document.title.includes('Antibot') && !document.title.includes('Challenge') && !document.title.includes('Доступ'),
                { timeout: 30000 }
            ).catch(() => { });

            title = await page.title();
            debugInfo.push(`Title after wait: ${title}`);
        }

        await page.waitForTimeout(3000);

        // Extract prices
        const priceData = await page.evaluate(() => {
            try {
                const nuxt = window.__NUXT__;
                if (nuxt) {
                    let ws = nuxt.state?.widgetStates || nuxt.widgetStates;
                    if (ws) {
                        const priceKey = Object.keys(ws).find(k => k.startsWith('webPrice-'));
                        if (priceKey) {
                            const priceJson = JSON.parse(ws[priceKey]);
                            return {
                                source: 'nuxt',
                                cardPrice: priceJson.cardPrice || null,
                                price: priceJson.price || null,
                                originalPrice: priceJson.originalPrice || null,
                                isAvailable: priceJson.isAvailable || false
                            };
                        }
                    }
                }
            } catch (e) { }

            try {
                const scripts = document.querySelectorAll('script[type="application/ld+json"]');
                for (const script of scripts) {
                    const data = JSON.parse(script.textContent);
                    if (data.offers?.price) {
                        return {
                            source: 'json_ld',
                            price: data.offers.price + ' ₽',
                            cardPrice: null,
                            originalPrice: null,
                            isAvailable: true
                        };
                    }
                }
            } catch (e) { }

            try {
                const pricesInPage = document.body.textContent.match(/(\d{1,3}(?:\s?\d{3})*)\s*₽/g);
                if (pricesInPage?.length > 0) {
                    return {
                        source: 'dom',
                        price: pricesInPage[0],
                        cardPrice: null,
                        originalPrice: null,
                        isAvailable: true,
                        allPrices: pricesInPage.slice(0, 10)
                    };
                }
            } catch (e) { }

            return null;
        });

        const sellerData = await page.evaluate(() => {
            try {
                const sellerLink = document.querySelector('a[href*="/seller/"]');
                if (sellerLink) {
                    const href = sellerLink.getAttribute('href');
                    const idMatch = href.match(/\/seller\/(\d+)/);
                    return {
                        name: sellerLink.textContent?.trim(),
                        externalId: idMatch ? idMatch[1] : null
                    };
                }
            } catch (e) { }
            return null;
        });

        await browser.close();

        if (!priceData) {
            return { error: 'Could not extract price data', success: false, debug: debugInfo };
        }

        const parsePrice = (priceStr) => {
            if (!priceStr) return null;
            const cleaned = priceStr.replace(/[^\d.,]/g, '').replace(',', '.');
            return parseFloat(cleaned) || null;
        };

        return {
            success: true,
            productId,
            userPrice: parsePrice(priceData.cardPrice) || parsePrice(priceData.price),
            basePrice: parsePrice(priceData.price),
            originalPrice: parsePrice(priceData.originalPrice),
            seller: sellerData,
            source: priceData.source,
            raw: priceData,
            debug: debugInfo
        };

    } catch (error) {
        try { await browser.close(); } catch (e) { }
        return { error: error.message, success: false, debug: debugInfo };
    }
}

const arg = process.argv[2];
if (!arg) {
    console.log(JSON.stringify({ error: 'Usage: node ozon_price_parser.js <product_url_or_id>', success: false }));
    process.exit(1);
}

const productId = await extractProductId(arg);
if (!productId) {
    console.log(JSON.stringify({ error: 'Could not extract product ID', success: false }));
    process.exit(1);
}

const result = await fetchPriceData(productId);
console.log(JSON.stringify(result, null, 2));
