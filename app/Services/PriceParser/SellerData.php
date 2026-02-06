<?php

declare(strict_types=1);

namespace App\Services\PriceParser;

class SellerData
{
    public function __construct(
        public string $name,
        public ?string $inn = null,
        public ?string $externalId = null
    ) {}
}
