<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductLink\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\MoonShine\Resources\Product\ProductResource;
use App\MoonShine\Resources\Marketplace\MarketplaceResource;

class ProductLinkIndexPage extends IndexPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Товар', 'product', resource: ProductResource::class)
                ->searchable()
                ->required(),
            BelongsTo::make('Маркетплейс', 'marketplace', resource: MarketplaceResource::class)
                ->required(),
            Url::make('URL', 'url')->required(),
            Text::make('История', 'price_histories_count', fn($item) => $item->priceHistories()->count())
                ->badge('purple'),
        ];
    }

    protected function topLayer(): array
    {
        return [
            ...parent::topLayer()
        ];
    }

    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer()
        ];
    }

    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer()
        ];
    }

    protected function buttons(): \MoonShine\Support\ListOf
    {
        return parent::buttons()->add(
            \MoonShine\UI\Components\ActionButton::make('Парсить')
                ->method('parsePrice')
                ->icon('play')
                ->primary()
        );
    }
}
