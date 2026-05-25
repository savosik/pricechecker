<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seller extends Model
{
    protected $fillable = [
        'name',
        'inn',
        'external_id',
        'marketplace_id',
        'email',
    ];

    public function marketplace(): BelongsTo
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(PriceHistory::class);
    }

    public function productLinks(): HasMany
    {
        return $this->hasMany(ProductLink::class);
    }
}
