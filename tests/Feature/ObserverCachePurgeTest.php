<?php

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Cache;

test('purges cache when menu item is updated or created', function () {
    Cache::put('umco_home_data', 'cached_content', 3600);
    expect(Cache::has('umco_home_data'))->toBeTrue();

    $category = MenuCategory::create([
        'name' => 'Signature Coffee',
        'slug' => 'signature-coffee',
        'sort' => 1,
    ]);

    expect(Cache::has('umco_home_data'))->toBeFalse();

    Cache::put('umco_home_data', 'cached_again', 3600);

    $item = MenuItem::create([
        'category_id' => $category->id,
        'name' => 'Kopi Aren UMCO',
        'slug' => 'kopi-aren-umco',
        'price' => 28000,
    ]);

    expect(Cache::has('umco_home_data'))->toBeFalse();
});
