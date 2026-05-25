<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'marketplace_id',
        'seller_id',
        'url',
        'settings',
        'last_parsed_at',
        'last_parse_error',
        'rrp_notified_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'last_parsed_at' => 'datetime',
        'rrp_notified_at' => 'datetime',
    ];

    /**
     * URL domain patterns mapped to marketplace codes.
     */
    protected static array $marketplacePatterns = [
        'ozon' => ['ozon.ru', 'ozon.com'],
        'wildberries' => ['wildberries.ru', 'wb.ru'],
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $link) {
            if (empty($link->marketplace_id) && !empty($link->url)) {
                $detected = static::detectMarketplaceFromUrl($link->url);
                if ($detected) {
                    $link->marketplace_id = $detected->id;
                }
            }
        });
    }

    /**
     * Detect marketplace by URL domain.
     */
    public static function detectMarketplaceFromUrl(string $url): ?Marketplace
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            return null;
        }

        $host = strtolower($host);

        foreach (static::$marketplacePatterns as $code => $domains) {
            foreach ($domains as $domain) {
                if (str_contains($host, $domain)) {
                    return Marketplace::where('code', $code)->first();
                }
            }
        }

        return null;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function marketplace(): BelongsTo
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(PriceHistory::class);
    }
}
