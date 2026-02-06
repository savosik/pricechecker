<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Brand\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use App\MoonShine\Resources\Brand\BrandResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<BrandResource, \App\Models\Brand>
 */
final class BrandFormPage extends FormPage
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
            ]),
        ];
    }

    protected function rules(\MoonShine\Contracts\Core\TypeCasts\DataWrapperContract $item): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}


