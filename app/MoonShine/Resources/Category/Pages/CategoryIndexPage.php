<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Category\Pages;

use App\Models\Category;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\Category\CategoryResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Preview;

/**
 * @extends IndexPage<CategoryResource>
 */
final class CategoryIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Preview::make('Название', 'name', function (Category $item) {
                $level = $item->getLevel();
                $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                $icon = $level > 0 ? '└─ ' : '';
                return $indent . $icon . $item->name;
            }),
            Preview::make('Товаров', 'products_count', function (Category $item) {
                return $item->products_count > 0 ? $item->products_count : '';
            }),
        ];
    }
}

