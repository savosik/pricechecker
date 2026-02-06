#!/usr/bin/env node
/**
 * Ozon Price Parser with Persistent Browser Context
 * 
 * Uses a persistent browser profile that retains cookies and session data
 * between runs. First run may require manual captcha solving.
 * 
 * Usage: node ozon_price_parser_persistent.js <product_url_or_id> [--interactive]
 * 
 * With --interactive flag, opens visible browser window for captcha solving
 */

import { chromium } from 'playwright';
import { existsSync, mkdirSync } from 'fs';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const USER_DATA_DIR = join(__dirname, '..', 'storage', 'ozon_browser_data');

// Ensure user data directory exists
if (!existsSync(USER_DATA_DIR)) {
    mkdirSync(USER_DATA_DIR, { recursive: true });
}

async function extractProductId(urlOrId) {
    if (/^\d+$/.test(urlOrId)) return urlOrId;
    const match = urlOrId.match(/\/product\/[^\/]*?(\d+)\/?/);
    if (match) return match[1];
    const numMatch = urlOrId.match(/(\d+)\/?$/);
    return numMatch ? numMatch[1] : null;
}

async function fetchPriceData(productId, interactive = false) {
    const debugInfo = [];

    // Launch browser with persistent context
    const context = await chromium.launchPersistentContext(USER_DATA_DIR, {
        headless: !interactive,
        args: [
            '--no-sandbox',
            '--disable-blink-features=AutomationControlled'
        ],
        userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        viewport: { width: 1920, height: 1080 },
        locale: 'ru-RU',
        timezoneId: 'Europe/Moscow'
    });

    try {
        // Override webdriver detection
        await context.addInitScript(() => {
            Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
        });

        const page = context.pages()[0] || await context.newPage();

        const url = `https://www.ozon.ru/product/${productId}/`;
        debugInfo.push(`Navigating to: ${url}`);
        debugInfo.push(`Interactive mode: ${interactive}`);
        debugInfo.push(`User data dir: ${USER_DATA_DIR}`);

        await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 60000 });

        // Wait for initial load
        await page.waitForTimeout(3000);

        let title = await page.title();
        debugInfo.push(`Page title: ${title}`);

        // Check for antibot
        const isBlocked = title.includes('Antibot') || title.includes('Challenge') || title.includes('Доступ');

        if (isBlocked) {
            if (interactive) {
                debugInfo.push('Antibot detected. Please solve the captcha in the browser window...');
                console.error('\n⚠️  CAPTCHA DETECTED - Please solve it in the browser window...\n');

                // Wait for user to solve captcha (up to 2 minutes)
                try {
                    await page.waitForFunction(
                        () => !document.title.includes('Antibot') &&
                            !document.title.includes('Challenge') &&
                            !document.title.includes('Доступ'),
                        { timeout: 120000 }
                    );
                    console.error('\n✅ Captcha solved! Continuing...\n');
                } catch (e) {
                    debugInfo.push('Timeout waiting for captcha solution');
                }
            } else {
                debugInfo.push('Antibot detected in headless mode. Try running with --interactive flag first.');
            }

            title = await page.title();
            debugInfo.push(`Title after wait: ${title}`);
        }

        // Wait for page content
        await page.waitForTimeout(2000);

        // Check for NUXT
        const hasNuxt = await page.evaluate(() => !!window.__NUXT__);
        debugInfo.push(`Has __NUXT__: ${hasNuxt}`);

        // Extract price data
        const priceData = await page.evaluate(() => {
            // Try NUXT
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
                                cardPrice: priceJson.cardPrice,
                                price: priceJson.price,
                                originalPrice: priceJson.originalPrice,
                                isAvailable: priceJson.isAvailable
                            };
                        }
                    }
                }
            } catch (e) { }

            // Try JSON-LD
            try {
                const scripts = document.querySelectorAll('script[type="application/ld+json"]');
                for (const script of scripts) {
                    const data = JSON.parse(script.textContent);
                    if (data.offers?.price) {
                        return {
                            source: 'json_ld',
                            price: data.offers.price + ' ₽',
                            isAvailable: data.offers.availability?.includes('InStock')
                        };
                    }
                }
            } catch (e) { }

            // Try DOM
            try {
                const prices = document.body.textContent.match(/(\d{1,3}(?:\s?\d{3})*)\s*₽/g);
                if (prices?.length) {
                    return { source: 'dom', price: prices[0], allPrices: prices.slice(0, 10), isAvailable: true };
                }
            } catch (e) { }

            return null;
        });

        // Extract seller
        const sellerData = await page.evaluate(() => {
            try {
                const link = document.querySelector('a[href*="/seller/"]');
                if (link) {
                    const match = link.href.match(/\/seller\/(\d+)/);
                    return { name: link.textContent?.trim(), externalId: match?.[1] };
                }
            } catch (e) { }
            return null;
        });

        await context.close();

        if (!priceData) {
            return { error: 'Could not extract price data', success: false, debug: debugInfo };
        }

        const parsePrice = (s) => {
            if (!s) return null;
            const n = parseFloat(s.replace(/[^\d.,]/g, '').replace(',', '.'));
            return isNaN(n) ? null : n;
        };

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
        try { await context.close(); } catch (e) { }
        return { error: error.message, success: false, debug: debugInfo };
    }
}

// Parse arguments
const args = process.argv.slice(2);
const interactive = args.includes('--interactive');
const urlOrId = args.find(a => !a.startsWith('--'));

if (!urlOrId) {
    console.log(JSON.stringify({
        error: 'Usage: node ozon_price_parser_persistent.js <product_url_or_id> [--interactive]',
        success: false
    }));
    process.exit(1);
}

const productId = await extractProductId(urlOrId);
if (!productId) {
    console.log(JSON.stringify({ error: 'Could not extract product ID', success: false }));
    process.exit(1);
}

const result = await fetchPriceData(productId, interactive);
console.log(JSON.stringify(result, null, 2));
