#!/usr/bin/env node
/**
 * Ozon Price Parser - Production Version
 * 
 * Uses persistent browser context to maintain session between runs.
 * First run may require --interactive flag to solve captcha manually.
 * 
 * Usage: 
 *   node ozon_price_parser.js <product_url_or_id>                # Headless mode
 *   node ozon_price_parser.js <product_url_or_id> --interactive  # With visible browser
 * 
 * Output: JSON with price data
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

function extractProductId(urlOrId) {
    if (/^\d+$/.test(urlOrId)) return urlOrId;

    // Match /product/any-text-123456789/ - the ID is the last number sequence before /
    const match = urlOrId.match(/\/product\/[^\/]*?-(\d+)\/?(?:\?|$)/);
    if (match) return match[1];

    // Match /product/123456789/ (just numeric ID)
    const numericMatch = urlOrId.match(/\/product\/(\d+)\/?/);
    if (numericMatch) return numericMatch[1];

    // Try to extract last number sequence from URL
    const lastNumMatch = urlOrId.match(/(\d{5,})/g);
    if (lastNumMatch) return lastNumMatch[lastNumMatch.length - 1];

    return null;
}

function parsePrice(priceStr) {
    if (!priceStr) return null;
    const cleaned = priceStr.replace(/[^\d.,]/g, '').replace(',', '.');
    const num = parseFloat(cleaned);
    return isNaN(num) ? null : num;
}

async function fetchPriceData(productId, interactive = false) {
    const debugInfo = [];

    const context = await chromium.launchPersistentContext(USER_DATA_DIR, {
        headless: !interactive,
        args: [
            '--no-sandbox',
            '--disable-blink-features=AutomationControlled',
            '--disable-dev-shm-usage'
        ],
        userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        viewport: { width: 1920, height: 1080 },
        locale: 'ru-RU',
        timezoneId: 'Europe/Moscow'
    });

    try {
        await context.addInitScript(() => {
            Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
        });

        const page = context.pages()[0] || await context.newPage();
        const url = `https://www.ozon.ru/product/${productId}/`;

        debugInfo.push(`Navigating to: ${url}`);
        await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 60000 });
        await page.waitForTimeout(3000);

        let title = await page.title();
        debugInfo.push(`Page title: ${title}`);

        // Check for antibot
        const isBlocked = title.includes('Antibot') || title.includes('Challenge') || title.includes('Доступ');

        if (isBlocked) {
            if (interactive) {
                console.error('\n⚠️  CAPTCHA DETECTED - Please solve it in the browser window...\n');
                try {
                    await page.waitForFunction(
                        () => !document.title.includes('Antibot') &&
                            !document.title.includes('Challenge') &&
                            !document.title.includes('Доступ'),
                        { timeout: 120000 }
                    );
                    console.error('\n✅ Captcha solved! Continuing...\n');
                } catch (e) {
                    debugInfo.push('Timeout waiting for captcha');
                }
            } else {
                debugInfo.push('Antibot detected. Run with --interactive flag to solve captcha.');
            }
            title = await page.title();
            debugInfo.push(`Title after wait: ${title}`);
        }

        await page.waitForTimeout(2000);

        // Extract price data using multiple methods
        const priceData = await page.evaluate(() => {
            const result = {};

            // Method 1: NUXT widgetStates
            try {
                const nuxt = window.__NUXT__;
                if (nuxt?.state?.widgetStates) {
                    const ws = nuxt.state.widgetStates;
                    const priceKey = Object.keys(ws).find(k => k.startsWith('webPrice-'));
                    if (priceKey) {
                        const data = JSON.parse(ws[priceKey]);
                        return {
                            source: 'nuxt',
                            cardPrice: data.cardPrice,
                            price: data.price,
                            originalPrice: data.originalPrice,
                            isAvailable: data.isAvailable
                        };
                    }
                }
            } catch (e) { }

            // Method 2: DOM webPrice widget
            try {
                const priceBlock = document.querySelector('[data-widget="webPrice"]');
                if (priceBlock) {
                    const text = priceBlock.textContent;
                    const cardPriceMatch = text.match(/(\d[\d\s]*)\s*₽\s*c Ozon Картой/);
                    const prices = {};

                    if (cardPriceMatch) {
                        prices.cardPrice = cardPriceMatch[1].trim() + ' ₽';
                    }

                    // Find regular price after card price
                    const afterCardText = text.substring(text.indexOf('c Ozon Картой') + 10);
                    const regularPriceMatch = afterCardText.match(/(\d[\d\s]*)\s*₽/);
                    if (regularPriceMatch) {
                        prices.price = regularPriceMatch[1].trim() + ' ₽';
                    }

                    if (prices.cardPrice || prices.price) {
                        return { source: 'dom', ...prices, isAvailable: true };
                    }
                }
            } catch (e) { }

            // Method 3: JSON-LD
            try {
                for (const script of document.querySelectorAll('script[type="application/ld+json"]')) {
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

            return null;
        });

        // Extract seller info
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

        return {
            success: true,
            productId,
            userPrice: parsePrice(priceData.cardPrice) || parsePrice(priceData.price),
            basePrice: parsePrice(priceData.price),
            originalPrice: parsePrice(priceData.originalPrice),
            isAvailable: priceData.isAvailable ?? true,
            seller: sellerData,
            source: priceData.source,
            debug: debugInfo
        };

    } catch (error) {
        try { await context.close(); } catch (e) { }
        return { error: error.message, success: false, debug: debugInfo };
    }
}

// Main
const args = process.argv.slice(2);
const interactive = args.includes('--interactive');
const urlOrId = args.find(a => !a.startsWith('--'));

if (!urlOrId) {
    console.log(JSON.stringify({
        error: 'Usage: node ozon_price_parser.js <product_url_or_id> [--interactive]',
        success: false
    }));
    process.exit(1);
}

const productId = extractProductId(urlOrId);
if (!productId) {
    console.log(JSON.stringify({ error: 'Could not extract product ID', success: false }));
    process.exit(1);
}

const result = await fetchPriceData(productId, interactive);
console.log(JSON.stringify(result));
