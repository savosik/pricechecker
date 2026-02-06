<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Marketplace\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\Marketplace\MarketplaceResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Color;

/**
 * @extends IndexPage<MarketplaceResource>
 */
final class MarketplaceIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Name', 'name')->sortable(),
            Text::make('Code', 'code')->sortable(),
            Color::make('Color', 'color'),
        ];
    }
}
