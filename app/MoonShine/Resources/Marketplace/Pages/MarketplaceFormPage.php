<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Marketplace\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use App\MoonShine\Resources\Marketplace\MarketplaceResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Color;

/**
 * @extends FormPage<MarketplaceResource>
 */
final class MarketplaceFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                Text::make('Name', 'name')->required(),
                Text::make('Code', 'code')->required(),
                Color::make('Color', 'color'),
            ]),
        ];
    }

    protected function rules(mixed $item): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:marketplaces,code,' . $item->getKey(),
            'color' => 'nullable|string|max:255',
        ];
    }
}
