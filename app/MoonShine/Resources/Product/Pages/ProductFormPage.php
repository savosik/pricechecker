<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Product\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use App\MoonShine\Resources\Product\ProductResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use App\MoonShine\Resources\Brand\BrandResource;
use App\MoonShine\Resources\Category\CategoryResource;
use App\MoonShine\Resources\PriceHistory\PriceHistoryResource;
use App\MoonShine\Resources\ProductLink\ProductLinkResource;
use App\MoonShine\Resources\Seller\SellerResource;

use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Select;
use App\Enums\Marketplace;
use MoonShine\UI\Components\ActionButton;
use MoonShine\Support\ListOf;

/**
 * @extends FormPage<ProductResource, \App\Models\Product>
 */
final class ProductFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Grid::make([
                Column::make([
                    Box::make([
                        ID::make(),
                        Text::make('Название', 'name')
                            ->required(),
                        Text::make('SKU', 'sku')
                            ->required(),
                        Url::make('URL изображения', 'image_url')
                            ->nullable(),

                    ]),
                    HasMany::make('Ссылки', 'links', resource: ProductLinkResource::class)
                        ->creatable()
                        ->fields([
                            ID::make(),
                            BelongsTo::make('Маркетплейс', 'marketplace', resource: \App\MoonShine\Resources\Marketplace\MarketplaceResource::class),
                            Url::make('URL'),
                            Text::make('История', 'price_histories_count', fn($item) => $item->priceHistories()->count())->badge('purple'),
                            \MoonShine\UI\Fields\Preview::make('Действия')
                                ->changeFill(fn($item) => \MoonShine\UI\Components\ActionButton::make('Парсить')
                                    ->method('parsePrice', params: ['resourceItem' => $item->getKey()])
                                    ->icon('play')
                                    ->primary()
                                    ->render()
                                )
                        ]),
                    Box::make([
                        Json::make('Условия записи цены', 'condition')
                            ->fields([
                                Select::make('Маркетплейс', 'marketplace_id')
                                    ->options(
                                        \App\Models\Marketplace::all()->pluck('name', 'id')->toArray()
                                    )
                                    ->required(),
                                Select::make('Тип цены', 'price_type')
                                    ->options([
                                        'base_price' => 'Базовая цена',
                                        'user_price' => 'Цена пользователя',
                                    ])
                                    ->required(),
                                Select::make('Условие', 'operator')
                                    ->options([
                                        'gt' => 'Больше',
                                        'gte' => 'Больше равно',
                                        'eq' => 'Равно',
                                        'lt' => 'Меньше',
                                        'lte' => 'Меньше равно',
                                    ])
                                    ->required(),
                                Number::make('Значение', 'value')
                                    ->required(),
                            ])
                            ->creatable()
                            ->removable(),
                    ]),
                    Box::make([
                        Json::make('Условия уведомления админов', 'notify_condition')
                            ->fields([
                                Select::make('Маркетплейс', 'marketplace_id')
                                    ->options(
                                        \App\Models\Marketplace::all()->pluck('name', 'id')->toArray()
                                    )
                                    ->required(),
                                Select::make('Тип цены', 'price_type')
                                    ->options([
                                        'base_price' => 'Базовая цена',
                                        'user_price' => 'Цена пользователя',
                                    ])
                                    ->required(),
                                Select::make('Условие', 'operator')
                                    ->options([
                                        'gt' => 'Больше',
                                        'gte' => 'Больше равно',
                                        'eq' => 'Равно',
                                        'lt' => 'Меньше',
                                        'lte' => 'Меньше равно',
                                    ])
                                    ->required(),
                                Number::make('Значение', 'value')
                                    ->required(),
                            ])
                            ->creatable()
                            ->removable(),
                    ]),
                    HasMany::make('История цен', 'priceHistories', resource: PriceHistoryResource::class)
                        ->creatable(false)
                        ->fields([
                            ID::make(),
                            BelongsTo::make('Маркетплейс', 'marketplace', resource: \App\MoonShine\Resources\Marketplace\MarketplaceResource::class),
                            Url::make('URL', 'url'),
                            Number::make('Цена пользователя', 'user_price'),
                            Number::make('Базовая цена', 'base_price'),
                            BelongsTo::make('Продавец', 'seller', resource: SellerResource::class)->nullable(),
                            Text::make('Проверено', 'checked_at'),
                        ]),
                ])->columnSpan(8),
                Column::make([
                    Collapse::make('Связи', [
                        BelongsTo::make('Бренд', 'brand', resource: BrandResource::class)
                            ->nullable()
                            ->searchable(),
                        BelongsToMany::make('Категории', 'categories', resource: CategoryResource::class)
                            ->tree('parent_id'),
                    ]),
                ])->columnSpan(4),
            ]),
        ];
    }

    protected function rules(\MoonShine\Contracts\Core\TypeCasts\DataWrapperContract $item): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'image_url' => 'nullable|url|max:255',
            'brand_id' => 'nullable|exists:brands,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons()->add(
            ActionButton::make('Парсить')
                ->method('parsePrices')
                ->icon('play')
                ->primary()
        );
    }
}
