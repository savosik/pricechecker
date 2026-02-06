<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_url',
        'sku',
        'name',
        'brand_id',
        'tracking_urls',
        'condition',
        'notify_condition',
    ];

    protected $casts = [
        'tracking_urls' => 'array',
        'condition' => 'collection',
        'notify_condition' => 'collection',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(PriceHistory::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(ProductLink::class);
    }
}

