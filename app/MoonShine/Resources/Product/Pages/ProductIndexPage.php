<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Product\Pages;

use App\Models\Product;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\Product\ProductResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use App\MoonShine\Resources\Brand\BrandResource;
use App\MoonShine\Resources\Category\CategoryResource;
use MoonShine\UI\Components\ActionButton;
use MoonShine\Support\ListOf;

/**
 * @extends IndexPage<ProductResource>
 */
final class ProductIndexPage extends IndexPage
{


    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Preview::make('Изображение', 'image_url', function (Product $product) {
                if (!$product->image_url) {
                    return '';
                }
                
                return '<img src="' . htmlspecialchars($product->image_url, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($product->name ?? '', ENT_QUOTES, 'UTF-8') . '" style="max-width: 100px; max-height: 100px; object-fit: contain;">';
            }),
            Text::make('SKU', 'sku')->sortable(),
            Text::make('Название', 'name')
                ->sortable()
                ->withoutTextWrap(),
            BelongsTo::make('Бренд', 'brand', resource: BrandResource::class)
                ->nullable(),
            BelongsToMany::make('Категории', 'categories', resource: CategoryResource::class)
                ->inLine(separator: ' ', badge: true),

            Preview::make('Ссылки', 'links_count', function (Product $product) {
                $count = $product->links_count ?? 0;
                return $count > 0 
                    ? '<span class="badge badge-primary">' . $count . '</span>' 
                    : '<span class="text-gray-400">0</span>';
            }),
            Preview::make('История', 'price_histories_count', function (Product $product) {
                $count = $product->price_histories_count ?? 0;
                return $count > 0 
                    ? '<span class="badge badge-success">' . $count . '</span>' 
                    : '<span class="text-gray-400">0</span>';
            }),
        ];
    }

    protected function filters(): iterable
    {
        return [
            Text::make('Название', 'name'),
            Text::make('SKU', 'sku'),
            BelongsTo::make('Бренд', 'brand', resource: BrandResource::class)
                ->nullable()
                ->searchable()
                ->valuesQuery(function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
                    return $query->select(['id', 'name']);
                }),
            BelongsToMany::make('Категории', 'categories', resource: CategoryResource::class)
                ->tree('parent_id')
                ->valuesQuery(function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
                    return $query->select(['id', 'name', 'parent_id']);
                }),
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

