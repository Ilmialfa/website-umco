<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use App\Models\PageSection;

class GalleryController extends Controller
{
    public function __invoke()
    {
        $sections = PageSection::where('page', 'galeri')
            ->where('is_active', true)
            ->orderBy('sort')
            ->get()
            ->keyBy('key');

        $items = GalleryItem::active()
            ->orderBy('sort')
            ->get();

        $groups = $items
            ->pluck('group')
            ->filter()
            ->unique()
            ->values();

        return view('pages.galeri', [
            'sections' => $sections,
            'hero' => $sections['hero'] ?? null,
            'items' => $items,
            'groups' => $groups,
        ]);
    }
}
