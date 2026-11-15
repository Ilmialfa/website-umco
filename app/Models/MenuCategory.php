<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MenuCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sort',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * @return HasMany<MenuItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'category_id')->orderBy('sort');
    }

    /**
     * @return HasMany<MenuItem, $this>
     */
    public function activeItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'category_id')
            ->where('is_active', true)
            ->orderBy('sort');
    }
}
