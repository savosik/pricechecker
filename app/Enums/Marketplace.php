<?php

declare(strict_types=1);

namespace App\Enums;

enum Marketplace: string
{
    case WB = 'WB';
    case OZ = 'OZ';

    public function color(): string
    {
        return match ($this) {
            self::WB => 'purple',
            self::OZ => 'blue',
        };
    }
}
