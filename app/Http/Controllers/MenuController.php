<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;

class MenuController extends Controller
{
    public function __invoke()
    {
        $categories = MenuCategory::where('is_active', true)
            ->orderBy('sort')
            ->with(['items' => function ($q) {
                $q->where('is_active', true)->orderBy('sort');
            }])
            ->get();

        $allItems = MenuItem::where('is_active', true)
            ->with('category')
            ->orderBy('sort')
            ->get();

        $bestSellers = MenuItem::where('is_active', true)
            ->where('is_favorite', true)
            ->with('category')
            ->orderBy('favorite_order')
            ->take(4)
            ->get();

        $itemsPayload = $allItems->map(function ($item) {
            return [
                'id' => $item->id,
                'category_id' => $item->category_id,
                'category_slug' => $item->category?->slug ?? '',
                'category_name' => $item->category?->name ?? '',
                'name' => $item->name,
                'slug' => $item->slug,
                'price' => $item->price,
                'price_formatted' => $item->formatted_price,
                'description' => $item->description,
                'image_url' => $item->image_url,
                'is_favorite' => (bool) $item->is_favorite,
                'is_available' => (bool) $item->is_available,
                'allergens' => $item->allergens ?? '',
                'tags' => $item->tags ?? [],
            ];
        });

        return view('pages.menu', [
            'categories' => $categories,
            'bestSellers' => $bestSellers,
            'itemsPayload' => $itemsPayload,
        ]);
    }
}
