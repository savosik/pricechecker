<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Seller\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\Seller\SellerResource;
use App\MoonShine\Resources\Marketplace\MarketplaceResource;
use App\Models\Marketplace;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Components\Layout\Flex;

/**
 * @extends IndexPage<SellerResource>
 */
final class SellerIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name')
                ->sortable()
                ->required(),
            Text::make('ИНН', 'inn'),
            Text::make('Внешний ID', 'external_id'),
            BelongsTo::make('Маркетплейс', 'marketplace', resource: MarketplaceResource::class)
                ->badge('primary')
                ->sortable(),
            \MoonShine\UI\Fields\Preview::make('Ссылки', 'product_links_count', fn($item) => $item->product_links_count)
                ->badge('info')
                ->sortable(),
            \MoonShine\UI\Fields\Preview::make('История', 'price_histories_count', fn($item) => $item->price_histories_count)
                ->badge('purple')
                ->sortable(),
        ];
    }

    protected function filters(): iterable
    {
        return [
            BelongsTo::make('Маркетплейс', 'marketplace', resource: MarketplaceResource::class)
                ->nullable(),
        ];
    }
}

