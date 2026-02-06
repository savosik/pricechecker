<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\PriceHistory\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\PriceHistory\PriceHistoryResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Url;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Range;
use MoonShine\UI\Fields\DateRange;
use App\Enums\Marketplace;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\MoonShine\Resources\Marketplace\MarketplaceResource;
use App\MoonShine\Resources\Product\ProductResource;
use App\MoonShine\Resources\Seller\SellerResource;
use App\Models\PriceHistory;

/**
 * @extends IndexPage<PriceHistoryResource>
 */
final class PriceHistoryIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Товар', 'product', resource: ProductResource::class)
                ->sortable(),
            BelongsTo::make('Маркетплейс', 'marketplace', resource: MarketplaceResource::class)
                ->sortable(),
            Url::make('URL', 'url'),
            Preview::make('Цена пользователя', 'user_price', function (PriceHistory $history) {
                if ($history->user_price === null) {
                    return '-';
                }
                return number_format((float)$history->user_price, 2, '.', ' ') . ' ₽';
            })->sortable(),
            Preview::make('Базовая цена', 'base_price', function (PriceHistory $history) {
                if ($history->base_price === null) {
                    return '-';
                }
                return number_format((float)$history->base_price, 2, '.', ' ') . ' ₽';
            })->sortable(),
            Date::make('Проверено', 'checked_at')
                ->sortable()
                ->format('d.m.Y H:i'),
            BelongsTo::make('Продавец', 'seller', resource: SellerResource::class)
                ->nullable()
                ->sortable(),
        ];
    }

    protected function filters(): iterable
    {
        return [
            BelongsTo::make('Товар', 'product', resource: ProductResource::class)
                ->nullable()
                ->searchable(),
            BelongsTo::make('МП', 'marketplace', resource: MarketplaceResource::class)
                ->nullable(),
            DateRange::make('Проверено', 'checked_at'),
            Range::make('Цена пользователя', 'user_price')
                ->min(0)
                ->max((float)(PriceHistory::max('user_price') ?? 100000))
                ->default([
                    'min' => 0,
                    'max' => (float)(PriceHistory::max('user_price') ?? 100000)
                ]),
            Range::make('Базовая цена', 'base_price')
                ->min(0)
                ->max((float)(PriceHistory::max('base_price') ?? 100000))
                ->default([
                    'min' => 0,
                    'max' => (float)(PriceHistory::max('base_price') ?? 100000)
                ]),
            BelongsTo::make('Продавец', 'seller', resource: SellerResource::class)
                ->nullable()
                ->searchable(),
        ];
    }


}

