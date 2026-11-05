<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\GalleryItem;
use App\Models\StoryBlock;
use App\Models\StreetStall;
use Illuminate\Support\Facades\Cache;

class AboutController extends Controller
{
    public function __invoke()
    {
        $data = Cache::remember('umco_about_data', 86400, function () {
            return [
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
            ];
        });

        return view('pages.about', $data);
    }
}
