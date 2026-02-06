<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
#[\MoonShine\MenuManager\Attributes\SkipMenu]

class Dashboard extends Page
{
    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Dashboard';
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
	{
		return [
            \MoonShine\UI\Components\Layout\Grid::make([
                // General Stats
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Всего товаров')
                        ->value(\App\Models\Product::count()),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Всего проверок')
                        ->value(\App\Models\PriceHistory::count()),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Средняя цена')
                        ->value(number_format((float) (\App\Models\PriceHistory::avg('user_price') ?? 0), 2) . ' ₽'),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Всего ссылок')
                        ->value(\App\Models\ProductLink::count()),
                ])->columnSpan(3),

                // Entities Stats
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Бренды')
                        ->value(\App\Models\Brand::count()),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Категории')
                        ->value(\App\Models\Category::count()),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Пользователи')
                        ->value(\App\Models\User::count()),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Активные товары')
                        ->value(\App\Models\Product::whereNotNull('tracking_urls')->count()),
                ])->columnSpan(3),

                // Link Stats
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Ссылки Ozon')
                        ->value(\App\Models\ProductLink::whereHas('marketplace', fn($q) => $q->where('code', 'ozon'))->count()),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Ссылки WB')
                        ->value(\App\Models\ProductLink::whereHas('marketplace', fn($q) => $q->where('code', 'wildberries'))->count()),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Сред. ссылок/товар')
                        ->value(number_format((float) (\App\Models\Product::withCount('links')->get()->avg('links_count') ?? 0), 1)),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Товары без ссылок')
                        ->value(\App\Models\Product::doesntHave('links')->count()),
                ])->columnSpan(3),

                // Price Stats
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Макс. цена')
                        ->value(number_format((float) (\App\Models\PriceHistory::max('user_price') ?? 0), 2) . ' ₽'),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Мин. цена')
                        ->value(number_format((float) (\App\Models\PriceHistory::min('user_price') ?? 0), 2) . ' ₽'),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Сред. цена Ozon')
                        ->value(number_format((float) (\App\Models\PriceHistory::whereHas('marketplace', fn($q) => $q->where('code', 'ozon'))->avg('user_price') ?? 0), 2) . ' ₽'),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Сред. цена WB')
                        ->value(number_format((float) (\App\Models\PriceHistory::whereHas('marketplace', fn($q) => $q->where('code', 'wildberries'))->avg('user_price') ?? 0), 2) . ' ₽'),
                ])->columnSpan(3),

                // Activity Stats
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Проверки сегодня')
                        ->value(\App\Models\PriceHistory::whereDate('created_at', today())->count()),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Проверки вчера')
                        ->value(\App\Models\PriceHistory::whereDate('created_at', today()->subDay())->count()),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('С уведомлениями')
                        ->value(\App\Models\Product::whereNotNull('notify_condition')->count()),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Бренды с товарами')
                        ->value(\App\Models\Brand::has('products')->count()),
                ])->columnSpan(3),

                // Extra
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Категории с товарами')
                        ->value(\App\Models\Category::has('products')->count()),
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Сироты (ссылки)')
                        ->value(\App\Models\ProductLink::doesntHave('product')->count()),
                ])->columnSpan(3),
            ])
        ];
	}
}
