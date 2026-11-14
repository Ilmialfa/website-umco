<?php

use App\Models\BeanOrigin;
use App\Models\CrowdPattern;
use App\Models\MenuItem;
use App\Models\PageSection;
use App\Models\Rule;
use App\Models\SeatingOption;
use App\Models\SpendTier;
use Database\Seeders\Redesign2026Seeder;

it('seeds all 39 menu items from folder and tiered reservation rules', function () {
    $this->seed(Redesign2026Seeder::class);

    expect(MenuItem::count())->toBe(39);
    expect(Rule::count())->toBe(7);
    expect(SeatingOption::count())->toBe(5);
    expect(BeanOrigin::count())->toBe(4);
    expect(CrowdPattern::count())->toBe(112); // 7 hari x 16 jam (07-22)
    expect(PageSection::where('page', 'home')->count())->toBe(13);

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
    expect(\App\Models\StoryBlock::where('body', 'like', '%huruf H%')->count())->toBe(0);
    expect(\App\Models\Testimonial::where('body', 'like', '%huruf H%')->count())->toBe(0);
    expect(\App\Models\GalleryItem::whereIn('group', ['indoor', 'outdoor'])->count())->toBe(0);
});
