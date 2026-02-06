<?php

declare(strict_types=1);

namespace App\Services\PriceParser;


use Illuminate\Support\Facades\Log;

class OzonPriceParser implements PriceParserInterface
{
    /**
     * Path to Node.js parser script
     */
    private const SCRIPT_PATH = 'scripts/ozon_price_parser.js';

    /**
     * Script execution timeout in seconds
     */
    private const TIMEOUT = 120;

    public function getPrice(string $url): ?PriceData
    {
        try {
            $scriptPath = base_path(self::SCRIPT_PATH);
            
            if (!file_exists($scriptPath)) {
                Log::error('OzonPriceParser: Script not found', ['path' => $scriptPath]);
                return null;
            }

            // Execute Node.js script
            $command = sprintf(
                'node %s %s 2>&1',
                escapeshellarg($scriptPath),
                escapeshellarg($url)
            );

            Log::info('OzonPriceParser: Executing script', ['command' => $command]);

            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            $jsonOutput = implode('', $output);
            $result = json_decode($jsonOutput, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('OzonPriceParser: Invalid JSON response', [
                    'url' => $url,
                    'output' => $jsonOutput,
                ]);
                return null;
            }

            if (!($result['success'] ?? false)) {
                Log::warning('OzonPriceParser: Script returned failure', [
                    'url' => $url,
                    'error' => $result['error'] ?? 'Unknown error',
                    'debug' => $result['debug'] ?? [],
                ]);
                return null;
            }

            // Build seller data if available
            $sellerData = null;
            if (!empty($result['seller'])) {
                $sellerData = new SellerData(
                    name: $result['seller']['name'] ?? 'Unknown',
                    inn: null, // Ozon doesn't expose seller INN
                    externalId: $result['seller']['externalId'] ?? null
                );
            }

            return new PriceData(
                userPrice: (float) ($result['userPrice'] ?? 0),
                basePrice: (float) ($result['basePrice'] ?? 0),
                sellerData: $sellerData
            );

        } catch (\Exception $e) {
            Log::error('OzonPriceParser: Exception during parsing', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
