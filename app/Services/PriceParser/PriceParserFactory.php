<?php

declare(strict_types=1);

namespace App\Services\PriceParser;

use App\Models\Marketplace;
use InvalidArgumentException;

class PriceParserFactory
{
    public static function make(Marketplace $marketplace): PriceParserInterface
    {
        // Assuming codes are 'ozon' and 'wb' or similar. 
        // I will use case-insensitive check or check against known codes.
        // Since I don't know exact codes in DB, I'll try to match by name or code.
        
        $code = strtolower($marketplace->code ?? $marketplace->name);

        if (str_contains($code, 'ozon')) {
            return new OzonPriceParser();
        }

        if (str_contains($code, 'wildberries') || str_contains($code, 'wb')) {
            return new WildberriesPriceParser();
        }

        throw new InvalidArgumentException("No parser found for marketplace: {$marketplace->name}");
    }
}
