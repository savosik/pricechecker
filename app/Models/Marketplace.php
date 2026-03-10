<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Marketplace extends Model
{
    protected $fillable = [
        'name',
        'code',
        'color',
    ];

    public function sellers(): HasMany
    {
        return $this->hasMany(Seller::class);
    }
}
