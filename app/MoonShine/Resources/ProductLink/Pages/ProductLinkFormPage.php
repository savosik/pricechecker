<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductLink\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\MoonShine\Resources\Product\ProductResource;
use App\MoonShine\Resources\Marketplace\MarketplaceResource;

use MoonShine\Laravel\Fields\Relationships\HasMany;
use App\MoonShine\Resources\PriceHistory\PriceHistoryResource;

class ProductLinkFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                BelongsTo::make('Товар', 'product', resource: ProductResource::class)
                    ->searchable()
                    ->asyncSearch()
                    ->required(),
                BelongsTo::make('Маркетплейс', 'marketplace', resource: MarketplaceResource::class)
                    ->required(),
                Url::make('URL', 'url')->required(),
            ]),

            HasMany::make('История цен', 'priceHistories', resource: PriceHistoryResource::class)
                ->readonly()
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
