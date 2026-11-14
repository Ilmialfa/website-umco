<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\PageSection;
use App\Support\IndonesianFormat;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    public function __invoke()
    {
        $data = Cache::remember('umco_menu_data', now()->addMinutes(30), function () {
            $hero = PageSection::where('page', 'menu')
                ->where('key', 'hero')
                ->first();

            $categories = MenuCategory::where('is_active', true)
                ->orderBy('sort')
                ->get(['id', 'name', 'slug', 'sort']);

            $allItems = MenuItem::where('is_active', true)
                ->with('category')
                ->orderBy('sort')
                ->get();

            $itemsPayload = $allItems->map(function (MenuItem $item) {
                $catSlug = $item->category?->slug ?? '';
                $tone = match ($catSlug) {
                    'kopi-hitam', 'kopi-susu' => 'bg-[var(--mentega)]/40',
                    'susu' => 'bg-[var(--blush)]',
                    'teh', 'mocktail' => 'bg-[var(--zaitun)]',
                    default => 'bg-[var(--kertas-terang)] rule-notebook border border-[var(--garis)]',
                };

                $img800 = $item->imageUrlFor(800, 'webp');

                return [
                    'id' => $item->id,
                    'category_id' => $item->category_id,
                    'category_slug' => $catSlug,
                    'category_name' => $item->category?->name ?? '',
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'price' => $item->price,
                    'price_formatted' => IndonesianFormat::rupiah($item->price),
                    'price_ribu' => IndonesianFormat::ribu($item->price),
                    'description' => $item->description,
                    'ingredients' => $item->ingredients ?: $item->description,
                    'has_temperature' => (bool) $item->has_temperature,
                    'variants' => $item->variants ?? [],
                    'is_favorite' => (bool) $item->is_favorite,
                    'is_new' => (bool) $item->is_new,
                    'is_available' => (bool) $item->is_available,
                    'is_placeholder' => (bool) ($item->is_placeholder || empty($img800)),
                    'image_480' => $item->imageUrlFor(480, 'webp'),
                    'image_800' => $img800,
                    'srcset_webp' => $item->srcsetFor('webp'),
                    'srcset_avif' => $item->srcsetFor('avif'),
                    'placeholder_tone' => $tone,
                ];
            })->values()->all();

            return [
                'hero' => $hero,
                'categories' => $categories,
                'itemsCount' => count($itemsPayload),
                'itemsPayload' => $itemsPayload,
            ];
        });

        return view('pages.menu', $data);
    }
}
