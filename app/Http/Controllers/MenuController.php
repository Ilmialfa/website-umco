<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\PageSection;
use App\Support\IndonesianFormat;
use Illuminate\Contracts\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
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

        $itemsPayload = $allItems->map(fn (MenuItem $item) => $this->transformItem($item))->values()->all();

        return view('pages.menu', [
            'hero' => $hero,
            'categories' => $categories,
            'itemsCount' => count($itemsPayload),
            'itemsPayload' => $itemsPayload,
        ]);
    }

    public function show(MenuItem $menuItem): View
    {
        abort_unless($menuItem->is_active, 404);
        $menuItem->load('category');

        $related = MenuItem::where('is_active', true)
            ->where('id', '!=', $menuItem->id)
            ->where(function ($q) use ($menuItem) {
                $q->where('category_id', $menuItem->category_id)
                    ->orWhere('is_favorite', true);
            })
            ->with('category')
            ->orderByRaw('CASE WHEN category_id = ? THEN 0 ELSE 1 END', [$menuItem->category_id])
            ->orderBy('sort')
            ->limit(4)
            ->get();

        return view('pages.menu-detail', [
            'item' => $menuItem,
            'itemData' => $this->transformItem($menuItem),
            'related' => $related,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function transformItem(MenuItem $item): array
    {
        $category = $item->category;
        $catSlug = $category !== null ? $category->slug : '';
        $tone = match ($catSlug) {
            'kopi-hitam', 'kopi-susu' => 'bg-[#E9EFEA]',
            'susu' => 'bg-[#EFE8DF]',
            'teh', 'mocktail' => 'bg-[#DCEBE1]',
            default => 'bg-[var(--kertas-terang)] rule-notebook',
        };

        $img800 = $item->imageUrlFor(800, 'webp');

        return [
            'id' => $item->id,
            'category_id' => $item->category_id,
            'category_slug' => $catSlug,
            'category_name' => $category !== null ? $category->name : '',
            'name' => $item->name,
            'slug' => $item->slug,
            'detail_url' => route('menu.show', $item->slug),
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
            'image_1200' => $item->imageUrlFor(1200, 'webp'),
            'srcset_webp' => $item->srcsetFor('webp'),
            'srcset_avif' => $item->srcsetFor('avif'),
            'placeholder_tone' => $tone,
        ];
    }
}
