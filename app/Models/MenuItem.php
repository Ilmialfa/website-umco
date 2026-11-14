<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'ingredients',
        'has_temperature',
        'price',
        'variants',
        'image_path',
        'is_placeholder',
        'image_variants',
        'is_favorite',
        'show_on_home',
        'favorite_order',
        'is_new',
        'is_available',
        'is_active',
        'sort',
    ];

    protected $casts = [
        'price' => 'integer',
        'variants' => 'array',
        'has_temperature' => 'boolean',
        'is_placeholder' => 'boolean',
        'image_variants' => 'array',
        'is_favorite' => 'boolean',
        'show_on_home' => 'boolean',
        'favorite_order' => 'integer',
        'is_new' => 'boolean',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function ($item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->name);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Ambil URL varian gambar sesuai ukuran (480 / 800 / 1200) dan format (webp / avif), fallback ke image_path.
     */
    public function imageUrlFor(int $width = 800, string $format = 'webp'): ?string
    {
        $variants = $this->image_variants ?? [];
        if (isset($variants[$format][(string) $width])) {
            return asset('storage/' . ltrim($variants[$format][(string) $width], '/'));
        }
        if (isset($variants['webp'][(string) $width])) {
            return asset('storage/' . ltrim($variants['webp'][(string) $width], '/'));
        }
        if (! empty($this->image_path)) {
            if (str_starts_with($this->image_path, 'http') || str_starts_with($this->image_path, '/')) {
                return $this->image_path;
            }

            return asset('storage/' . ltrim($this->image_path, '/'));
        }

        return null;
    }

    public function srcsetFor(string $format = 'webp'): string
    {
        $variants = $this->image_variants[$format] ?? [];
        $parts = [];
        foreach ([480, 800, 1200] as $w) {
            if (isset($variants[(string) $w])) {
                $parts[] = asset('storage/' . ltrim($variants[(string) $w], '/')) . " {$w}w";
            }
        }

        return implode(', ', $parts);
    }
}
