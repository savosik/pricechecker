<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\PriceHistory;

use App\Models\PriceHistory;
use MoonShine\Laravel\Resources\ModelResource;
use App\MoonShine\Resources\PriceHistory\Pages\PriceHistoryFormPage;
use App\MoonShine\Resources\PriceHistory\Pages\PriceHistoryIndexPage;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;

use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;

/**
 * @extends ModelResource<PriceHistory>
 */
#[Icon('cube')]
#[Group('Парсер цен', 'price-parser', translatable: false)]
#[Order(3)]
class PriceHistoryResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    protected string $model = PriceHistory::class;

    protected string $title = 'История цен';

    protected string $column = 'id';

    protected array $with = ['product'];

    protected function pages(): array
    {
        return [
            PriceHistoryIndexPage::class,
            PriceHistoryFormPage::class,
        ];
    }

    public function search(): array
    {
        return [
            'id',
            'product.name',
            'product.sku',
            'url',
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
            \MoonShine\UI\Fields\Text::make('SKU', 'product.sku'),
            \MoonShine\UI\Fields\Text::make('Бренд', 'product.brand.name'),
            \MoonShine\UI\Fields\Text::make('Маркетплейс', 'marketplace.name'),
            \MoonShine\UI\Fields\Url::make('URL', 'url'),
            \MoonShine\UI\Fields\Number::make('Цена пользователя', 'user_price'),
            \MoonShine\UI\Fields\Number::make('Базовая цена', 'base_price'),
            \MoonShine\UI\Fields\Date::make('Проверено', 'checked_at')->format('d.m.Y H:i'),
            \MoonShine\UI\Fields\Text::make('Продавец', 'seller.name'),
        ];
    }

    public function actions(): array
    {
        return [
            \MoonShine\UI\Components\ActionButton::make(
                'Удалить все записи',
                route('moonshine.price-history.delete-all')
            )
            ->error()
            ->icon('trash')
            ->withConfirm(
                'Удалить все записи?',
                'Это действие необратимо',
                'Удалить'
            )
        ];
    }

    public function queryTags(): array
    {
        $tags = [];

        // Marketplace Tags
        $marketplaces = \App\Models\Marketplace::all();
        foreach ($marketplaces as $marketplace) {
            $tags[] = QueryTag::make(
                $marketplace->name,
                fn(\Illuminate\Database\Eloquent\Builder $query) => $query->where('marketplace_id', $marketplace->id)
            );
        }

        // Date Tags
        $tags[] = QueryTag::make(
            'Сегодня',
            fn(\Illuminate\Database\Eloquent\Builder $query) => $query->whereDate('created_at', \Carbon\Carbon::today())
        );

        $tags[] = QueryTag::make(
            'Вчера',
            fn(\Illuminate\Database\Eloquent\Builder $query) => $query->whereDate('created_at', \Carbon\Carbon::yesterday())
        );

        $tags[] = QueryTag::make(
            'На этой неделе',
            fn(\Illuminate\Database\Eloquent\Builder $query) => $query->whereBetween('created_at', [
                \Carbon\Carbon::now()->startOfWeek(),
                \Carbon\Carbon::now()->endOfWeek()
            ])
        );

        $tags[] = QueryTag::make(
            'В этом месяце',
            fn(\Illuminate\Database\Eloquent\Builder $query) => $query->whereMonth('created_at', \Carbon\Carbon::now()->month)
                                                                    ->whereYear('created_at', \Carbon\Carbon::now()->year)
        );

        return $tags;
    }
}

