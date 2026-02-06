<?php

declare(strict_types=1);

namespace App\Services\PriceParser;

class PriceData
{
    public function __construct(
        public float $userPrice,
        public float $basePrice,
        public ?SellerData $sellerData = null
    ) {}
}
