<?php

declare(strict_types=1);

namespace App\Services\PriceParser;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WildberriesPriceParser implements PriceParserInterface
{
    /**
     * API endpoint for getting product details with prices
     */
    private const API_ENDPOINT = 'https://u-card.wb.ru/cards/v4/detail';

    /**
     * Default destination code (Moscow region)
     */
    private const DEFAULT_DEST = -1257786;

    /**
     * Default currency
     */
    private const DEFAULT_CURRENCY = 'rub';

    public function getPrice(string $url): ?PriceData
    {
        try {
            // Extract product ID from URL
            $productId = $this->extractProductId($url);
            if (!$productId) {
                Log::warning('WildberriesPriceParser: Could not extract product ID from URL', ['url' => $url]);
                return null;
            }

            // Fetch price data from API
            $response = Http::timeout(60)
                ->withOptions([
                    'connect_timeout' => 20,
                ])
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                    'Accept' => 'application/json',
                ])
                ->get(self::API_ENDPOINT, [
                    'appType' => 1,
                    'curr' => self::DEFAULT_CURRENCY,
                    'dest' => self::DEFAULT_DEST,
                    'spp' => 30,
                    'lang' => 'ru',
                    'ab_testing' => 'false',
                    'nm' => $productId,
                ]);

            if (!$response->successful()) {
                Log::warning('WildberriesPriceParser: API request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data = $response->json();
            return $this->parseResponse($data, $productId);

        } catch (\Exception $e) {
            Log::error('WildberriesPriceParser: Error parsing price', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Extract product ID (nm) from Wildberries URL
     * 
     * Supports formats:
     * - https://www.wildberries.ru/catalog/123456789/detail.aspx
     * - https://www.wildberries.by/catalog/123456789/detail.aspx
     * - https://wildberries.ru/catalog/123456789/
     */
    private function extractProductId(string $url): ?string
    {
        // Pattern to match the product ID in the URL
        if (preg_match('/\/catalog\/(\d+)\/?/', $url, $matches)) {
            return $matches[1];
        }

        // Also try to match nm parameter in query string
        if (preg_match('/[?&]nm=(\d+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Parse API response and extract price and seller data
     */
    private function parseResponse(array $data, string $productId): ?PriceData
    {
        $products = $data['products'] ?? [];
        
        if (empty($products)) {
            Log::warning('WildberriesPriceParser: No products in API response', ['productId' => $productId]);
            return null;
        }

        $product = $products[0];

        // Find first size with price
        $priceInfo = null;
        foreach ($product['sizes'] ?? [] as $size) {
            if (isset($size['price'])) {
                $priceInfo = $size['price'];
                break;
            }
        }

        if (!$priceInfo) {
            Log::warning('WildberriesPriceParser: No price found in product sizes', ['productId' => $productId]);
            return null;
        }

        // Prices are in kopecks, convert to rubles
        $basePrice = ($priceInfo['basic'] ?? 0) / 100;
        $userPrice = ($priceInfo['product'] ?? 0) / 100;

        // Build seller data
        $sellerData = null;
        if (isset($product['supplier'])) {
            $sellerData = new SellerData(
                name: $product['supplier'],
                inn: null, // INN not available in this API
                externalId: isset($product['supplierId']) ? (string) $product['supplierId'] : null
            );
        }

        return new PriceData(
            userPrice: round($userPrice, 2),
            basePrice: round($basePrice, 2),
            sellerData: $sellerData
        );
    }
}
