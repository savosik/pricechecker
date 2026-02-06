<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Marketplace as MarketplaceEnum; // Keep for now if needed elsewhere, but model uses Entity
use App\Models\Marketplace;

class PriceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'marketplace',
        'user_price',
        'base_price',
        'product_id',
        'price',
        'marketplace_id',
        'product_link_id',
        'seller_id',
        'url',
        'checked_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'checked_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function marketplace(): BelongsTo
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(ProductLink::class, 'product_link_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
}
