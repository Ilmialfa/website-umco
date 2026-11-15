<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\ClosedDate;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\MenuItem;
use App\Models\PageSection;
use App\Models\SpendTier;
use App\Models\Testimonial;
use App\Services\InstagramFeedService;

class HomeController extends Controller
{
    public function __invoke()
    {
        $sections = PageSection::where('page', 'home')
            ->where('is_active', true)
            ->orderBy('sort')
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

        $activeBanners = Banner::active()->get();

        $diagonalGallery = GalleryItem::active()
            ->whereIn('orientation', ['diagonal', 'mixed'])
            ->orderBy('sort')
            ->limit(14)
            ->get();

        $spendTiers = SpendTier::orderBy('party_min')->get();

        $closedDates = ClosedDate::where('date', '>=', today()->format('Y-m-d'))
            ->get()
            ->mapWithKeys(fn ($row) => [$row->date->format('Y-m-d') => $row->reason ?: 'Tutup'])
            ->all();

        return view('pages.home', [
            'sections' => $sections,
            'activeBanners' => $activeBanners,
            'favoriteMenuItems' => $favoriteMenuItems,
            'spendTiers' => $spendTiers,
            'closedDates' => $closedDates,
            'faqs' => Faq::active()->get(),
            'testimonials' => Testimonial::where('is_featured', true)->orderBy('sort')->limit(9)->get(),
            'instagramFeed' => app(InstagramFeedService::class)->getCachedOrFallback(8),
            'diagonalGallery' => $diagonalGallery,
            'galleryItems' => $diagonalGallery,
        ]);
    }
}
