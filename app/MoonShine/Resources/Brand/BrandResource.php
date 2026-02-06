<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Brand;

use App\Models\Brand;
use MoonShine\Laravel\Resources\ModelResource;
use App\MoonShine\Resources\Brand\Pages\BrandFormPage;
use App\MoonShine\Resources\Brand\Pages\BrandIndexPage;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;

/**
 * @extends ModelResource<Brand>
 */
#[Icon('tag')]
#[Group('Каталог', 'catalog', translatable: false)]
#[Order(1)]
class BrandResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    protected string $model = Brand::class;

    protected string $title = 'Бренды';

    protected string $column = 'name';

    protected array $with = [];

    protected function pages(): array
    {
        return [
            BrandIndexPage::class,
            BrandFormPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
        ];
        return [
            'id',
            'name',
        ];
    }

    /**
     * @return ?\MoonShine\Crud\Handlers\Handler
     */
    public function import(): ?\MoonShine\Crud\Handlers\Handler
    {
        return null;
    }

    /**
     * @return list<FieldContract>
     */
    public function exportFields(): iterable
    {
        return [
            \MoonShine\UI\Fields\ID::make(),
            \MoonShine\UI\Fields\Text::make('Название', 'name'),
        ];
    }

    protected function modifyQueryBuilder(\Illuminate\Contracts\Database\Eloquent\Builder $builder): \Illuminate\Contracts\Database\Eloquent\Builder
    {
        return $builder->withCount('products');
    }
}


