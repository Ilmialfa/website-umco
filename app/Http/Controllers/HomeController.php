<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Facility;
use App\Models\MenuItem;
use App\Models\Testimonial;
use App\Support\Settings;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __invoke()
    {
        $data = Cache::remember('umco_home_data', 86400, function () {
            return [
                'banners' => Banner::active()->get(),
                'favoriteMenuItems' => MenuItem::where('is_active', true)
                    ->where('is_favorite', true)
                    ->orderBy('favorite_order')
                    ->with('category')
                    ->take(6)
                    ->get(),
                'facilities' => Facility::where('is_active', true)
                    ->orderBy('sort')
                    ->take(6)
                    ->get(),
                'testimonials' => Testimonial::where('is_featured', true)
                    ->orderBy('sort')
                    ->take(4)
                    ->get(),
            ];
        });

        return view('pages.home', $data);
    }
}
