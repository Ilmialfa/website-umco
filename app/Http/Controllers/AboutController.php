<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\GalleryItem;
use App\Models\StoryBlock;
use App\Models\StreetStall;

class AboutController extends Controller
{
    public function __invoke()
    {
        return view('pages.cerita', [
            'storyBlocks' => StoryBlock::orderBy('sort')->get(),
            'facilities' => Facility::where('is_active', true)
                ->orderBy('sort')
                ->get(),
            'streetStalls' => StreetStall::where('is_active', true)
                ->orderBy('sort')
                ->get(),
            'galleryItems' => GalleryItem::where('is_active', true)
                ->orderBy('sort')
                ->get(),
        ]);
    }
}
