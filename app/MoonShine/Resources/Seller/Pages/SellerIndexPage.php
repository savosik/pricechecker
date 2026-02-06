<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Seller\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\Seller\SellerResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

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
            \MoonShine\UI\Fields\Preview::make('История', 'price_histories_count', fn($item) => $item->price_histories_count)
                ->badge('purple')
                ->sortable(),
        ];
    }
}
