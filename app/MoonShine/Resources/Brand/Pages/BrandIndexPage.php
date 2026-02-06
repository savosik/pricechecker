<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Brand\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\Brand\BrandResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Preview;
use App\Models\Brand;

/**
 * @extends IndexPage<BrandResource>
 */
final class BrandIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name')->sortable(),
            Preview::make('Товаров', 'products_count', function (Brand $item) {
                return $item->products_count > 0 ? $item->products_count : '';
            }),
        ];
    }
}


