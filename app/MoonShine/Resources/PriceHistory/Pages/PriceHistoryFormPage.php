<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\PriceHistory\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use App\MoonShine\Resources\PriceHistory\PriceHistoryResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Text;
use App\MoonShine\Resources\Marketplace\MarketplaceResource;
use App\MoonShine\Resources\Seller\SellerResource;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\MoonShine\Resources\Product\ProductResource;

/**
 * @extends FormPage<PriceHistoryResource, \App\Models\PriceHistory>
 */
final class PriceHistoryFormPage extends FormPage
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
                    ->required()
                    ->searchable(),
                BelongsTo::make('Маркетплейс', 'marketplace', resource: MarketplaceResource::class)
                    ->required(),
                Text::make('URL', 'url')
                    ->nullable(),
                Number::make('Цена пользователя', 'user_price')
                    ->nullable()
                    ->min(0)
                    ->step(0.01),
                Number::make('Базовая цена', 'base_price')
                    ->nullable()
                    ->min(0)
                    ->step(0.01),
                Date::make('Проверено', 'checked_at')
                    ->nullable()
                    ->withTime(),
                BelongsTo::make('Продавец', 'seller', resource: SellerResource::class)
                    ->nullable()
                    ->searchable(),
            ]),
        ];
    }

    protected function rules(\MoonShine\Contracts\Core\TypeCasts\DataWrapperContract $item): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'user_price' => 'nullable|numeric|min:0',
            'base_price' => 'nullable|numeric|min:0',
            'checked_at' => 'nullable|date',
            'url' => 'nullable|string',
            'marketplace_id' => ['required', 'exists:marketplaces,id'],
            'seller_id' => 'nullable|exists:sellers,id',
        ];
    }
}

