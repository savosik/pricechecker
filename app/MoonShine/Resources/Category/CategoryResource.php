<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Category;

use App\Models\Category;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Laravel\Resources\ModelResource;
use App\MoonShine\Resources\Category\Pages\CategoryFormPage;
use App\MoonShine\Resources\Category\Pages\CategoryIndexPage;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;

/**
 * @extends ModelResource<Category>
 */
#[Icon('folder')]
#[Group('Каталог', 'catalog', translatable: false)]
#[Order(2)]
class CategoryResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    protected string $model = Category::class;

    protected string $title = 'Категории';

    protected string $column = 'name';

    protected array $with = ['parent'];

    protected function pages(): array
    {
        return [
            CategoryIndexPage::class,
            CategoryFormPage::class,
        ];
    }

    protected function search(): array
    {
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
            \MoonShine\UI\Fields\Text::make('Родитель', 'parent.name'),
        ];
    }

    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        // Загружаем все категории с родителями для построения дерева
        $categories = Category::with('parent')->get();
        
        // Строим дерево категорий
        $tree = $this->buildTree($categories);
        
        // Получаем плоский список в правильном порядке
        $orderedIds = $this->flattenTree($tree);
        
        // Сортируем по порядку дерева
        if (!empty($orderedIds)) {
            $builder->orderByRaw('FIELD(id, ' . implode(',', $orderedIds) . ')');
        } else {
            // Если нет категорий, просто сортируем по имени
            $builder->orderBy('name');
        }
        
        return $builder->withCount('products');
    }

    /**
     * Строит дерево категорий
     */
    private function buildTree($categories, $parentId = null): array
    {
        $tree = [];
        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $tree[] = [
                    'category' => $category,
                    'children' => $this->buildTree($categories, $category->id),
                ];
            }
        }
        return $tree;
    }

    /**
     * Преобразует дерево в плоский список с сохранением порядка
     */
    private function flattenTree(array $tree, int $level = 0): array
    {
        $result = [];
        foreach ($tree as $item) {
            $result[] = $item['category']->id;
            if (!empty($item['children'])) {
                $result = array_merge($result, $this->flattenTree($item['children'], $level + 1));
            }
        }
        return $result;
    }
}

