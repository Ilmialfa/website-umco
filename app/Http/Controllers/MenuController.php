<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    public function __invoke()
    {
        $data = Cache::remember('umco_menu_data', 86400, function () {
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

            // Prepare lean JSON payload for client-side instant search & filter
            $itemsPayload = $allItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'category_id' => $item->menu_category_id,
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

            return [
                'categories' => $categories,
                'bestSellers' => $bestSellers,
                'itemsPayload' => $itemsPayload,
            ];
        });

        return view('pages.menu', $data);
    }
}
