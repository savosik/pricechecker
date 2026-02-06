#!/usr/bin/env python3
"""
Ozon Price Parser using undetected-chromedriver
Better at bypassing antibot protection than standard Selenium/Playwright
"""

import sys
import json
import re
import time

try:
    import undetected_chromedriver as uc
    from selenium.webdriver.common.by import By
    from selenium.webdriver.support.ui import WebDriverWait
    from selenium.webdriver.support import expected_conditions as EC
except ImportError as e:
    print(json.dumps({"error": f"Missing dependency: {e}", "success": False}))
    sys.exit(1)


def extract_product_id(url_or_id: str) -> str | None:
    """Extract product ID from URL or return as-is if already numeric"""
    if url_or_id.isdigit():
        return url_or_id
    
    # Match /product/name-123456789/ or /product/123456789/
    match = re.search(r'/product/[^/]*?(\d+)/?', url_or_id)
    if match:
        return match.group(1)
    
    # Try to find trailing number
    match = re.search(r'(\d+)/?$', url_or_id)
    if match:
        return match.group(1)
    
    return None


def parse_price(price_str: str | None) -> float | None:
    """Parse price string to float"""
    if not price_str:
        return None
    cleaned = re.sub(r'[^\d.,]', '', price_str).replace(',', '.')
    try:
        return float(cleaned)
    except ValueError:
        return None


def fetch_price_data(product_id: str) -> dict:
    """Fetch price data from Ozon using undetected Chrome"""
    debug_info = []
    
    # Chrome options
    options = uc.ChromeOptions()
    options.add_argument('--no-sandbox')
    options.add_argument('--disable-dev-shm-usage')
    options.add_argument('--disable-gpu')
    options.add_argument('--window-size=1920,1080')
    options.add_argument('--lang=ru-RU')
    
    # Run headless
    options.add_argument('--headless=new')
    
    driver = None
    try:
        debug_info.append("Starting undetected Chrome...")
        driver = uc.Chrome(options=options, version_main=131)
        
        url = f"https://www.ozon.ru/product/{product_id}/"
        debug_info.append(f"Navigating to: {url}")
        
        driver.get(url)
        
        # Wait for page to load
        time.sleep(5)
        
        title = driver.title
        debug_info.append(f"Page title: {title}")
        
        # Check for antibot
        if 'Antibot' in title or 'Challenge' in title or 'Доступ' in title:
            debug_info.append("Antibot detected, waiting...")
            time.sleep(10)
            title = driver.title
            debug_info.append(f"Title after wait: {title}")
        
        # Try to extract price data from NUXT
        price_data = driver.execute_script("""
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
            } catch (e) {}
            
            // Try JSON-LD
            try {
                const scripts = document.querySelectorAll('script[type="application/ld+json"]');
                for (const script of scripts) {
                    const data = JSON.parse(script.textContent);
                    if (data.offers && data.offers.price) {
                        return {
                            source: 'json_ld',
                            price: data.offers.price + ' ₽',
                            cardPrice: null,
                            originalPrice: null,
                            isAvailable: true
                        };
                    }
                }
            } catch (e) {}
            
            // Try DOM
            try {
                const prices = document.body.textContent.match(/(\\d{1,3}(?:\\s?\\d{3})*)\\s*₽/g);
                if (prices && prices.length > 0) {
                    return {
                        source: 'dom',
                        price: prices[0],
                        cardPrice: null,
                        originalPrice: null,
                        isAvailable: true,
                        allPrices: prices.slice(0, 10)
                    };
                }
            } catch (e) {}
            
            return null;
        """)
        
        # Try to get seller
        seller_data = driver.execute_script("""
            try {
                const link = document.querySelector('a[href*="/seller/"]');
                if (link) {
                    const href = link.getAttribute('href');
                    const match = href.match(/\\/seller\\/(\\d+)/);
                    return {
                        name: link.textContent?.trim() || null,
                        externalId: match ? match[1] : null
                    };
                }
            } catch (e) {}
            return null;
        """)
        
        driver.quit()
        
        if not price_data:
            return {
                "error": "Could not extract price data",
                "success": False,
                "debug": debug_info
            }
        
        return {
            "success": True,
            "productId": product_id,
            "userPrice": parse_price(price_data.get('cardPrice')) or parse_price(price_data.get('price')),
            "basePrice": parse_price(price_data.get('price')),
            "originalPrice": parse_price(price_data.get('originalPrice')),
            "isAvailable": price_data.get('isAvailable', False),
            "seller": seller_data,
            "source": price_data.get('source'),
            "raw": price_data,
            "debug": debug_info
        }
        
    except Exception as e:
        if driver:
            try:
                driver.quit()
            except:
                pass
        return {
            "error": str(e),
            "success": False,
            "debug": debug_info
        }


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Usage: python ozon_price_parser.py <product_url_or_id>", "success": False}))
        sys.exit(1)
    
    product_id = extract_product_id(sys.argv[1])
    if not product_id:
        print(json.dumps({"error": "Could not extract product ID", "success": False}))
        sys.exit(1)
    
    result = fetch_price_data(product_id)
    print(json.dumps(result, indent=2, ensure_ascii=False))
