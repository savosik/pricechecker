<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_category');
    }

    /**
     * Вычисляет уровень вложенности категории
     * Использует рекурсивный запрос для вычисления уровня
     */
    public function getLevel(): int
    {
        static $levelsCache = [];
        
        // Если уровень уже вычислен, возвращаем из кэша
        if (isset($levelsCache[$this->id])) {
            return $levelsCache[$this->id];
        }
        
        $level = 0;
        $parentId = $this->parent_id;
        
        // Если нет родителя, уровень 0
        if (!$parentId) {
            $levelsCache[$this->id] = 0;
            return 0;
        }
        
        // Загружаем родителя, если он еще не загружен
        if (!$this->relationLoaded('parent')) {
            $this->load('parent');
        }
        
        $parent = $this->parent;
        if (!$parent) {
            $levelsCache[$this->id] = 0;
            return 0;
        }
        
        // Рекурсивно вычисляем уровень родителя и добавляем 1
        $level = $parent->getLevel() + 1;
        $levelsCache[$this->id] = $level;
        
        return $level;
    }
}

