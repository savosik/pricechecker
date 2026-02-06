<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Category\Pages;

use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Laravel\Pages\Crud\FormPage;
use App\MoonShine\Resources\Category\CategoryResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Field;

/**
 * @extends FormPage<CategoryResource, \App\Models\Category>
 */
final class CategoryFormPage extends FormPage
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
                BelongsTo::make('Родительская категория', 'parent', resource: CategoryResource::class)
                    ->nullable()
                    ->searchable()
                    ->valuesQuery(function (Builder $query, Field $field) {
                        $item = $this->getResource()->getItem();
                        if ($item && $item->getKey()) {
                            $query->where('id', '!=', $item->getKey());
                        }
                        return $query;
                    }),
            ]),
        ];
    }

    protected function rules(\MoonShine\Contracts\Core\TypeCasts\DataWrapperContract $item): array
    {
        return [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ];
    }
}

