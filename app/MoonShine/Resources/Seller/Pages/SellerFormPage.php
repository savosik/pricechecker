<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Seller\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use App\MoonShine\Resources\Seller\SellerResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<SellerResource>
 */
final class SellerFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [

            Box::make([
                ID::make(),
                Text::make('Название', 'name')
                    ->required(),
                Text::make('ИНН', 'inn'),
                Text::make('Внешний ID', 'external_id'),
            ]),

            \MoonShine\Laravel\Fields\Relationships\HasMany::make('История цен', 'priceHistories', resource: \App\MoonShine\Resources\PriceHistory\PriceHistoryResource::class)
                ->creatable(false)
                ->fields([
                    ID::make(),
                    \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('Товар', 'product', resource: \App\MoonShine\Resources\Product\ProductResource::class),
                    \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('Маркетплейс', 'marketplace', resource: \App\MoonShine\Resources\Marketplace\MarketplaceResource::class),
                    \MoonShine\UI\Fields\Number::make('Цена пользователя', 'user_price'),
                    \MoonShine\UI\Fields\Number::make('Базовая цена', 'base_price'),
                    \MoonShine\UI\Fields\Date::make('Проверено', 'checked_at')
                        ->format('d.m.Y H:i'),
                ]),
        ];
    }

    protected function rules(mixed $item): array
    {
        return [
            'name' => 'required|string|max:255',
            'inn' => 'nullable|string|max:255',
            'external_id' => 'nullable|string|max:255',
        ];
    }
}
