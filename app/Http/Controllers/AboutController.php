<?php

namespace App\Http\Controllers;

use App\Models\BeanOrigin;
use App\Models\GalleryItem;
use App\Models\PageSection;
use App\Models\StoryBlock;
use App\Models\StreetStall;
use Illuminate\Support\Facades\Cache;

class AboutController extends Controller
{
    public function __invoke()
    {
        $data = Cache::remember('umco_cerita_data', now()->addMinutes(30), function () {
            $sections = PageSection::where('page', 'cerita')
                ->where('is_active', true)
                ->orderBy('sort')
                ->get()
                ->keyBy('key');

            $storyBlocks = StoryBlock::where('page', 'cerita')->orderBy('sort')->get();
            if ($storyBlocks->isEmpty()) {
                $storyBlocks = StoryBlock::orderBy('sort')->get();
            }

            $allGallery = GalleryItem::where('is_active', true)->orderBy('sort')->get();
            $extraGallery = $allGallery->count() > 6 ? $allGallery->slice(4)->take(6)->values() : $allGallery;

            return [
                'sections' => $sections,
                'beanOrigins' => BeanOrigin::orderBy('altitude')->get(),
                'storyBlocks' => $storyBlocks,
                'streetStalls' => StreetStall::where('is_active', true)->orderBy('sort')->get(),
                'galleryItems' => $extraGallery,
            ];
        });

        return view('pages.cerita', $data);
    }
}
