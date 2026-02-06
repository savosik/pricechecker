<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Marketplace;

use Illuminate\Database\Eloquent\Model;
use App\Models\Marketplace;
use App\MoonShine\Resources\Marketplace\Pages\MarketplaceIndexPage;
use App\MoonShine\Resources\Marketplace\Pages\MarketplaceFormPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Attributes\Icon;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;

/**
 * @extends ModelResource<Marketplace>
 */
#[Icon('shopping-bag')]
class MarketplaceResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    protected string $model = Marketplace::class;

    protected string $title = 'Marketplaces';

    protected string $column = 'name';

    protected function pages(): array
    {
        return [
            MarketplaceIndexPage::class,
            MarketplaceFormPage::class,
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
            \MoonShine\UI\Fields\Text::make('Initial', 'name'),
            \MoonShine\UI\Fields\Text::make('Code', 'code'),
            \MoonShine\UI\Fields\Color::make('Color', 'color'),
        ];
    }
}
