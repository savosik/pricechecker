<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductLink;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductLink;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;
use MoonShine\UI\Fields\Select;
use App\Models\Marketplace;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\Support\Attributes\Icon;
use App\MoonShine\Resources\ProductLink\Pages\ProductLinkIndexPage;
use App\MoonShine\Resources\ProductLink\Pages\ProductLinkFormPage;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;

/**
 * @extends ModelResource<ProductLink>
 */
#[Icon('link')]
#[Group('Парсер цен', 'price-parser', translatable: false)]
class ProductLinkResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    protected string $model = ProductLink::class;

    protected string $title = 'Ссылки';

    protected string $column = 'id';

    public function search(): array
    {
        return [
            'id',
            'url',
            'product.name',
            'product.sku',
            'marketplace.name',
            'product.brand.name',
            'product.categories.name',
        ];
    }

    protected function pages(): array
    {
        return [
            ProductLinkIndexPage::class,
            ProductLinkFormPage::class,
        ];
    }

    protected function rules(mixed $item): array
    {
        return [
            'marketplace_id' => 'required|exists:marketplaces,id',
            'url' => 'required|url',
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
            \MoonShine\UI\Fields\Text::make('Товар', 'product.name'),
            \MoonShine\UI\Fields\Text::make('Маркетплейс', 'marketplace.name'),
            \MoonShine\UI\Fields\Url::make('URL', 'url'),
        ];
    }

    public function actions(): array
    {
        return [
            \MoonShine\UI\Components\ActionButton::make('Парсить')
                ->method('parsePrice')
                ->icon('play')
                ->primary()
        ];
    }

    #[\MoonShine\Support\Attributes\AsyncMethod]
    public function parsePrice(\Illuminate\Http\Request $request): void
    {
        $linkId = $request->get('resourceItem') ?? $request->route('resourceItem');
        $link = ProductLink::findOrFail($linkId);

        if (empty($link->marketplace_id) || empty($link->url)) {
            throw new \Exception('Некорректная ссылка');
        }

        $marketplace = $link->marketplace;
        $queue = $marketplace ? strtolower($marketplace->code ?: $marketplace->name) : 'default';

        \App\Jobs\ParseProductPriceJob::dispatch($link)->onQueue($queue);
    }
}
