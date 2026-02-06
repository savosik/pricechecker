<?php

declare(strict_types=1);

namespace App\Services\PriceParser;

use Illuminate\Support\Facades\Log;

class DomPriceExtractor
{
    /**
     * Extract prices from Ozon HTML
     * Ozon uses data-widget="webPrice" to contain price information
     * Prices can be in RUB (₽) or BYN format: "21,14 BYN" or "1 234 ₽"
     */
    public function extractOzonPrice(string $dom): ?PriceData
    {
        try {
            $sellerData = $this->extractOzonSeller($dom);
            
            // Method 1: Extract from webPrice widget (most reliable)
            if (preg_match('/data-widget="webPrice"[^>]*>(.{0,3000})/s', $dom, $widgetMatch)) {
                $priceBlock = $widgetMatch[1];
                
                // Extract all prices from the block
                // Pattern for BYN: "21,14 BYN" or "378,11 BYN"
                // Pattern for RUB: "1 234 ₽" or "1234,50 ₽"
                $prices = [];
                
                // BYN format: digits with optional comma for decimals, then BYN
                if (preg_match_all('/([\d\s]+[,.]?\d*)\s*BYN/u', $priceBlock, $bynMatches)) {
                    foreach ($bynMatches[1] as $priceStr) {
                        $price = $this->parsePrice($priceStr);
                        if ($price > 0 && $price < 100000) {
                            $prices[] = $price;
                        }
                    }
                }
                
                // RUB format: digits with spaces, then ₽
                if (preg_match_all('/([\d\s]+[,.]?\d*)\s*₽/u', $priceBlock, $rubMatches)) {
                    foreach ($rubMatches[1] as $priceStr) {
                        $price = $this->parsePrice($priceStr);
                        if ($price > 0 && $price < 10000000) {
                            $prices[] = $price;
                        }
                    }
                }
                
                if (!empty($prices)) {
                    // First price is usually the sale price (discounted)
                    // Second price is the original price (if exists)
                    $userPrice = $prices[0];
                    $basePrice = count($prices) > 1 ? $prices[1] : $prices[0];
                    
                    Log::info('DomPriceExtractor: Ozon prices extracted', [
                        'userPrice' => $userPrice,
                        'basePrice' => $basePrice,
                        'allPrices' => $prices,
                    ]);
                    
                    return new PriceData(
                        userPrice: $userPrice,
                        basePrice: $basePrice,
                        sellerData: $sellerData
                    );
                }
            }
            
            // Method 2: Look for JSON-LD structured data
            if (preg_match('/"@type"\s*:\s*"Product".*?"price"\s*:\s*"?([\d.,]+)"?/s', $dom, $jsonLdMatch)) {
                $userPrice = $this->parsePrice($jsonLdMatch[1]);
                if ($userPrice > 0) {
                    return new PriceData(
                        userPrice: $userPrice,
                        basePrice: $userPrice,
                        sellerData: $sellerData
                    );
                }
            }
            
            // Method 3: Fallback - search for any reasonable price pattern
            // Look in the whole DOM for price patterns, but be more selective
            $prices = [];
            
            // Match BYN prices
            if (preg_match_all('/([\d\s]+[,.]?\d{0,2})\s*BYN/u', $dom, $allBynMatches)) {
                foreach ($allBynMatches[1] as $priceStr) {
                    $price = $this->parsePrice($priceStr);
                    if ($price > 1 && $price < 100000) {
                        $prices[] = $price;
                    }
                }
            }
            
            // Match RUB prices
            if (preg_match_all('/([\d\s]+[,.]?\d{0,2})\s*₽/u', $dom, $allRubMatches)) {
                foreach ($allRubMatches[1] as $priceStr) {
                    $price = $this->parsePrice($priceStr);
                    if ($price > 1 && $price < 10000000) {
                        $prices[] = $price;
                    }
                }
            }
            
            if (!empty($prices)) {
                // Get unique prices and sort
                $prices = array_unique($prices);
                sort($prices);
                
                // Filter out very small prices (likely ratings, counts, etc.)
                $prices = array_filter($prices, fn($p) => $p > 10);
                $prices = array_values($prices);
                
                if (!empty($prices)) {
                    $userPrice = $prices[0]; // Smallest price (sale)
                    $basePrice = end($prices); // Largest price (original)
                    
                    return new PriceData(
                        userPrice: $userPrice,
                        basePrice: $basePrice,
                        sellerData: $sellerData
                    );
                }
            }
            
            Log::warning('DomPriceExtractor: Could not find Ozon price in DOM');
            return null;
            
        } catch (\Exception $e) {
            Log::error('DomPriceExtractor: Error extracting Ozon price', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
    
    /**
     * Parse price string to float
     * Handles both comma and dot as decimal separator
     * Removes spaces used as thousand separators
     */
    private function parsePrice(string $priceStr): float
    {
        // Remove spaces
        $cleaned = preg_replace('/\s/', '', $priceStr);
        // Replace comma with dot for decimal
        $cleaned = str_replace(',', '.', $cleaned);
        // Handle cases like "1.234.56" -> "1234.56"
        if (substr_count($cleaned, '.') > 1) {
            $lastDot = strrpos($cleaned, '.');
            $cleaned = str_replace('.', '', substr($cleaned, 0, $lastDot)) . substr($cleaned, $lastDot);
        }
        return (float) $cleaned;
    }
    
    /**
     * Extract seller info from Ozon HTML
     * Important: seller (продавец) is NOT the same as brand (бренд/производитель)
     * Seller link format: /seller/name-id/ or href="https://ozon.by/seller/name-id/"
     */
    private function extractOzonSeller(string $dom): ?SellerData
    {
        // Method 1 (Priority): Look for seller link - this is the ACTUAL seller
        // Pattern: /seller/sellername-123456/ or ozon.ru/seller/sellername-123456/
        if (preg_match('/\/seller\/([^\/]+)-(\d+)\/?/', $dom, $sellerLinkMatch)) {
            $sellerSlug = $sellerLinkMatch[1];
            $sellerId = $sellerLinkMatch[2];
            
            // Clean up seller name from slug (replace dashes with spaces, capitalize)
            $sellerName = str_replace(['-', '_'], ' ', $sellerSlug);
            $sellerName = ucwords(strtolower($sellerName));
            
            if (!empty($sellerName) && strlen($sellerName) > 1) {
                Log::info('DomPriceExtractor: Ozon seller found via seller link', [
                    'name' => $sellerName,
                    'id' => $sellerId,
                ]);
                return new SellerData(
                    name: $sellerName,
                    inn: null,
                    externalId: $sellerId
                );
            }
        }
        
        // Method 2: Try to find seller in sellerName JSON field
        if (preg_match('/"sellerName"\s*:\s*"([^"]+)"/', $dom, $nameMatch)) {
            $sellerId = null;
            if (preg_match('/"sellerId"\s*:\s*(\d+)/', $dom, $idMatch)) {
                $sellerId = $idMatch[1];
            }
            
            Log::info('DomPriceExtractor: Ozon seller found via sellerName', ['name' => $nameMatch[1]]);
            return new SellerData(
                name: $nameMatch[1],
                inn: null,
                externalId: $sellerId
            );
        }
        
        // Method 3: Look for seller in webSeller widget
        if (preg_match('/data-widget="webSeller"[^>]*>.*?<a[^>]*>([^<]+)</s', $dom, $widgetMatch)) {
            $sellerName = trim(strip_tags($widgetMatch[1]));
            if (!empty($sellerName) && strlen($sellerName) > 1) {
                Log::info('DomPriceExtractor: Ozon seller found via webSeller widget', ['name' => $sellerName]);
                return new SellerData(
                    name: $sellerName,
                    inn: null,
                    externalId: null
                );
            }
        }
        
        // Method 4: Fallback to brand (this is manufacturer, NOT seller)
        if (preg_match('/"brand"\s*:\s*"([^"]+)"/', $dom, $brandMatch)) {
            $brandName = trim($brandMatch[1]);
            if (!empty($brandName) && strlen($brandName) > 1 && strlen($brandName) < 100) {
                Log::info('DomPriceExtractor: Ozon using brand as fallback seller', ['brand' => $brandName]);
                return new SellerData(
                    name: $brandName,
                    inn: null,
                    externalId: null
                );
            }
        }
        
        return null;
    }

    /**
     * Extract prices from Wildberries HTML (backup method)
     */
    public function extractWildberriesPrice(string $dom): ?PriceData
    {
        try {
            $sellerData = $this->extractWildberriesSeller($dom);
            
            // Look for price in JSON data
            if (preg_match('/"priceU"\s*:\s*(\d+)/', $dom, $matches)) {
                // WB prices are in kopecks * 100
                $userPrice = (float) $matches[1] / 100;
                
                $basePrice = $userPrice;
                if (preg_match('/"salePriceU"\s*:\s*(\d+)/', $dom, $saleMatches)) {
                    $basePrice = (float) $saleMatches[1] / 100;
                }
                
                return new PriceData(
                    userPrice: $userPrice,
                    basePrice: $basePrice,
                    sellerData: $sellerData
                );
            }
            
            // Alternative pattern matching
            if (preg_match_all('/(\d[\d\s]*)\s*₽/u', $dom, $matches)) {
                $prices = [];
                foreach ($matches[1] as $priceStr) {
                    $price = (float) preg_replace('/\s/', '', $priceStr);
                    if ($price > 0 && $price < 10000000) {
                        $prices[] = $price;
                    }
                }
                
                if (!empty($prices)) {
                    sort($prices);
                    return new PriceData(
                        userPrice: $prices[0],
                        basePrice: end($prices),
                        sellerData: $sellerData
                    );
                }
            }
            
            Log::warning('DomPriceExtractor: Could not find Wildberries price in DOM');
            return null;
            
        } catch (\Exception $e) {
            Log::error('DomPriceExtractor: Error extracting Wildberries price', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
    
    /**
     * Extract seller info from Wildberries HTML
     */
    private function extractWildberriesSeller(string $dom): ?SellerData
    {
        if (preg_match('/"supplierName"\s*:\s*"([^"]+)"/', $dom, $nameMatch)) {
            $sellerId = null;
            if (preg_match('/"supplierId"\s*:\s*(\d+)/', $dom, $idMatch)) {
                $sellerId = $idMatch[1];
            }
            
            return new SellerData(
                name: $nameMatch[1],
                inn: null,
                externalId: $sellerId
            );
        }
        
        return null;
    }
}
