<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seller extends Model
{
    protected $fillable = [
        'name',
        'inn',
        'external_id',
    ];

    public function priceHistories(): HasMany
    {
        return $this->hasMany(PriceHistory::class);
    }
}
