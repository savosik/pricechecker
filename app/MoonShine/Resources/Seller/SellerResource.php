<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Seller;

use Illuminate\Database\Eloquent\Model;
use App\Models\Seller;
use App\MoonShine\Resources\Seller\Pages\SellerIndexPage;
use App\MoonShine\Resources\Seller\Pages\SellerFormPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Attributes\Icon;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;

/**
 * @extends ModelResource<Seller>
 */
#[Icon('users')]
class SellerResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    protected string $model = Seller::class;

    protected string $title = 'Продавцы';

    protected string $column = 'name';

    protected function pages(): array
    {
        return [
            SellerIndexPage::class,
            SellerFormPage::class,
        ];
    }
    
    // We can keep search logic here or move to pages. Usually search relies on the resource configuration unless overridden in IndexPage.
    // Default search uses fields() from resource if no search() method. But here fields are in pages.
    // So distinct search() method is good.
    public function search(): array
    {
        return ['name', 'inn', 'external_id'];
    }

    protected function rules(mixed $item): array
    {
       // Rules are also checked in FormPage if defined there. 
       // We can duplicate or keep it here as fallback. 
       // For this refactor, I put rules in FormPage as well.
        return [
            'name' => 'required|string|max:255',
            'inn' => 'nullable|string|max:255',
            'external_id' => 'nullable|string|max:255',
        ];
    }

    public function modifyQueryBuilder(\Illuminate\Contracts\Database\Eloquent\Builder $builder): \Illuminate\Contracts\Database\Eloquent\Builder
    {
        return $builder->withCount(['priceHistories']);
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
            \MoonShine\UI\Fields\Text::make('ИНН', 'inn'),
            \MoonShine\UI\Fields\Text::make('Внешний ID', 'external_id'),
        ];
    }
}
