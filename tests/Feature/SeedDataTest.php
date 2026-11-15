<?php

use App\Models\Banner;
use App\Models\BeanOrigin;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\MenuItem;
use App\Models\PageSection;
use App\Models\Rule;
use App\Models\SeatingOption;
use App\Models\SpendTier;
use App\Models\StoryBlock;
use App\Models\Testimonial;
use Database\Seeders\Redesign2026Seeder;

it('seeds all 39 menu items from folder and tiered reservation rules', function () {
    $this->seed(Redesign2026Seeder::class);

    expect(MenuItem::count())->toBe(39);
    expect(Rule::count())->toBe(7);
    expect(SeatingOption::count())->toBe(5);
    expect(BeanOrigin::count())->toBe(4);
    expect(PageSection::where('page', 'home')->count())->toBe(10);
    expect(Faq::count())->toBeGreaterThanOrEqual(6);
    expect(Banner::count())->toBeGreaterThanOrEqual(2);
    expect(GalleryItem::where('orientation', 'portrait')->count())->toBeGreaterThanOrEqual(5);
    expect(Testimonial::count())->toBeGreaterThanOrEqual(6);

    expect(SpendTier::minSpendFor(2))->toBe(50000);
    expect(SpendTier::minSpendFor(3))->toBe(100000);
    expect(SpendTier::minSpendFor(4))->toBe(125000);
    expect(SpendTier::minSpendFor(5))->toBe(150000);
    expect(SpendTier::minSpendFor(6))->toBe(175000);
    expect(SpendTier::minSpendFor(7))->toBe(200000);

    // Pastikan Americano memiliki 3 varian sesuai foto
    $americano = MenuItem::where('slug', 'americano')->first();
    expect($americano)->not->toBeNull();
    expect($americano->variants)->toHaveCount(3);
    expect($americano->price)->toBe(24000);

    // Pastikan tidak ada sisa konsep lama di database
    expect(StoryBlock::where('body', 'like', '%huruf H%')->count())->toBe(0);
    expect(Testimonial::where('body', 'like', '%huruf H%')->count())->toBe(0);
    expect(GalleryItem::whereIn('group', ['indoor', 'outdoor'])->count())->toBe(0);
});
