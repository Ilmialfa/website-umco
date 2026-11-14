<?php

namespace App\Http\Controllers;

use App\Models\ClosedDate;
use App\Models\CrowdPattern;
use App\Models\GalleryItem;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\PageSection;
use App\Models\Rule;
use App\Models\SpendTier;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __invoke()
    {
        $sections = PageSection::where('page', 'home')
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $categories = MenuCategory::where('is_active', true)
            ->orderBy('sort')
            ->with([
                'items' => fn ($q) => $q
                    ->where('is_active', true)
                    ->orderByDesc('show_on_home')
                    ->orderBy('sort')
                    ->limit(4),
            ])
            ->limit(4)
            ->get();

        $favoriteMenuItems = MenuItem::with('category')
            ->where('is_active', true)
            ->where('is_favorite', true)
            ->orderBy('favorite_order')
            ->limit(8)
            ->get();

        if ($favoriteMenuItems->isEmpty()) {
            $favoriteMenuItems = MenuItem::with('category')
                ->where('is_active', true)
                ->orderBy('sort')
                ->limit(8)
                ->get();
        }

        $crowdPatterns = CrowdPattern::all()
            ->groupBy('day_of_week')
            ->map(fn ($rows) => $rows->pluck('level', 'hour')->all())
            ->all();

        $spendTiers = SpendTier::orderBy('party_min')->get();

        $closedDates = ClosedDate::where('date', '>=', today()->format('Y-m-d'))
            ->get()
            ->mapWithKeys(fn ($row) => [$row->date->format('Y-m-d') => $row->reason ?: 'Tutup'])
            ->all();

        return view('pages.home', [
            'sections' => $sections,
            'menuCategories' => $categories,
            'favoriteMenuItems' => $favoriteMenuItems,
            'soldOutCount' => MenuItem::where('is_active', true)->where('is_available', false)->count(),
            'crowdPatterns' => $crowdPatterns,
            'spendTiers' => $spendTiers,
            'closedDates' => $closedDates,
            'rules' => Rule::where('is_active', true)->orderBy('sort')->get(),
            'testimonials' => Testimonial::where('is_featured', true)->orderBy('sort')->get(),
            'galleryItems' => GalleryItem::where('is_active', true)->orderBy('sort')->limit(12)->get(),
        ]);
    }
}
