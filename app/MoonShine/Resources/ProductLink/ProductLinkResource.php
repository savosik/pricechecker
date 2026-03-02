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
            'seller.name',
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
            'marketplace_id' => 'nullable|exists:marketplaces,id',
            'seller_id' => [
                'nullable',
                'exists:sellers,id',
                function (string $attribute, mixed $value, \Closure $fail) use ($item) {
                    if ($value === null) {
                        return;
                    }

                    $productId = request()->input('product_id');
                    $marketplaceId = request()->input('marketplace_id');

                    // If marketplace not explicitly set, try to detect from URL
                    if (!$marketplaceId) {
                        $url = request()->input('url');
                        if ($url) {
                            $detected = \App\Models\ProductLink::detectMarketplaceFromUrl($url);
                            $marketplaceId = $detected?->id;
                        }
                    }

                    if (!$productId || !$marketplaceId) {
                        return;
                    }

                    $query = \App\Models\ProductLink::where('product_id', $productId)
                        ->where('marketplace_id', $marketplaceId)
                        ->where('seller_id', $value);

                    // Exclude current record when editing
                    $itemId = $item->getKey() ?? null;
                    if ($itemId) {
                        $query->where('id', '!=', $itemId);
                    }

                    if ($query->exists()) {
                        $seller = \App\Models\Seller::find($value);
                        $marketplace = \App\Models\Marketplace::find($marketplaceId);
                        $fail("Ссылка с продавцом \"{$seller->name}\" на маркетплейсе \"{$marketplace->name}\" уже существует для этого товара.");
                    }
                },
            ],
            'url' => [
                'required',
                'url',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $marketplaceId = request()->input('marketplace_id');

                    // If marketplace is explicitly selected, no need to auto-detect
                    if ($marketplaceId) {
                        return;
                    }

                    // Marketplace not selected — must be detectable from URL
                    $detected = \App\Models\ProductLink::detectMarketplaceFromUrl($value);
                    if (!$detected) {
                        $fail('Не удалось определить маркетплейс по URL. Укажите маркетплейс вручную или используйте ссылку с ozon.ru / wildberries.ru.');
                    }
                },
            ],
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
            \MoonShine\UI\Fields\Text::make('Продавец', 'seller.name'),
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
