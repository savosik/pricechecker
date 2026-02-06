<?php

declare(strict_types=1);

namespace App\Services\PriceParser;



interface PriceParserInterface
{
    public function getPrice(string $url): ?PriceData;
}
