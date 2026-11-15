<?php

namespace App\Http\Controllers;

use App\Models\BeanOrigin;
use App\Models\Facility;
use App\Models\GalleryItem;
use App\Models\PageSection;
use App\Models\StoryBlock;
use App\Models\StreetStall;

class AboutController extends Controller
{
    public function __invoke()
    {
        $sections = PageSection::where('page', 'cerita')
            ->where('is_active', true)
            ->orderBy('sort')
            ->get()
            ->keyBy('key');

        $storyBlocks = StoryBlock::where('page', 'cerita')->orderBy('sort')->get();
        if ($storyBlocks->isEmpty()) {
            $storyBlocks = StoryBlock::orderBy('sort')->get();
        }

        $facilities = Facility::where('is_active', true)->orderBy('sort')->get();
        $allGallery = GalleryItem::where('is_active', true)->orderBy('sort')->get();
        $extraGallery = $allGallery->count() > 6 ? $allGallery->slice(4)->take(6)->values() : $allGallery;

        return view('pages.cerita', [
            'sections' => $sections,
            'beanOrigins' => BeanOrigin::orderBy('altitude')->get(),
            'storyBlocks' => $storyBlocks,
            'facilities' => $facilities,
            'streetStalls' => StreetStall::where('is_active', true)->orderBy('sort')->get(),
            'galleryItems' => $extraGallery,
        ]);
    }
}
