<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Product;

use App\Models\Product;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Laravel\Resources\ModelResource;
use App\MoonShine\Resources\Product\Pages\ProductFormPage;
use App\MoonShine\Resources\Product\Pages\ProductIndexPage;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use App\Jobs\ParseProductPriceJob;
use MoonShine\Support\Attributes\AsyncMethod;
use MoonShine\Laravel\Notifications\MoonShineNotification;
use MoonShine\Support\Enums\ToastType;

use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;

/**
 * @extends ModelResource<Product>
 */
#[Icon('cube')]
#[Group('Каталог', 'catalog', translatable: false)]
#[Order(3)]
class ProductResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;
    protected string $model = Product::class;

    protected string $title = 'Товары';

    protected string $column = 'name';

    protected array $with = ['brand', 'categories'];

    protected function pages(): array
    {
        return [
            ProductIndexPage::class,
            ProductFormPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
            'sku',
            'brand.name',
            'categories.name',
        ];
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::MASS_DELETE)->except(Action::DELETE);
    }

    public function modifyQueryBuilder(Builder $builder): Builder
    {
        return $builder->withCount(['priceHistories', 'links']);
    }

    /**
     * @return list<FieldContract>
     */
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
            \MoonShine\UI\Fields\Text::make('SKU', 'sku'),
            \MoonShine\UI\Fields\Url::make('URL изображения', 'image_url'),
            \MoonShine\UI\Fields\Text::make('Бренд', 'brand.name'),
        ];
    }



    /**
     * @return list<QueryTag>
     */
    public function queryTags(): array
    {
        return [
            QueryTag::make(
                'С ссылками',
                fn(Builder $query) => $query->whereHas('links')
            ),
            QueryTag::make(
                'Без ссылок',
                fn(Builder $query) => $query->whereDoesntHave('links')
            ),
            QueryTag::make(
                'С историей',
                fn(Builder $query) => $query->whereHas('priceHistories')
            ),
            QueryTag::make(
                'Без истории',
                fn(Builder $query) => $query->whereDoesntHave('priceHistories')
            ),
        ];
    }



    public function actions(): array
    {
        return [
            ActionButton::make('Парсить')
                ->method('parsePrices')
                ->icon('play')
                ->primary(),
        ];
    }



    #[AsyncMethod]
    public function parsePrices(\Illuminate\Http\Request $request): void
    {
        // Get product ID from request or route parameter
        $productId = $request->get('resourceItem') ?? $request->route('resourceItem');
        $item = Product::findOrFail($productId);
        
        $links = $item->links;
        
        \Log::info('Parsing prices for product', [
            'product_id' => $item->id,
            'links_count' => $links->count(),
        ]);

        if ($links->isEmpty()) {
            throw new \Exception('У товара нет ссылок для отслеживания');
        }

        $count = 0;
        foreach ($links as $link) {
            if (empty($link->marketplace_id) || empty($link->url)) {
                continue;
            }

            $marketplace = $link->marketplace;
            $queue = $marketplace ? strtolower($marketplace->code ?: $marketplace->name) : 'default';

            ParseProductPriceJob::dispatch(
                $link
            )->onQueue($queue);
            
            $count++;
        }

        // Success - no exception means it worked
    }
    #[AsyncMethod]
    public function parsePrice(\Illuminate\Http\Request $request): void
    {
        $linkId = $request->get('resourceItem') ?? $request->route('resourceItem');
        $link = \App\Models\ProductLink::findOrFail($linkId);

        if (empty($link->marketplace_id) || empty($link->url)) {
            throw new \Exception('Некорректная ссылка');
        }

        $marketplace = $link->marketplace;
        $queue = $marketplace ? strtolower($marketplace->code ?: $marketplace->name) : 'default';

        \App\Jobs\ParseProductPriceJob::dispatch($link)->onQueue($queue);
    }
}

