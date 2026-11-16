<?php

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\PageSection;
use App\Models\User;
use App\Services\InstagramFeedService;
use App\Support\Settings;
use Database\Seeders\Redesign2026Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

test('application timezone defaults to Asia/Jakarta (P0-01)', function () {
    expect(config('app.timezone'))->toBe('Asia/Jakarta');
});

test('instagram fallback hides fabricated engagement metrics and serves webp assets (P0-03, P0-04, P2-07)', function () {
    Cache::forget(InstagramFeedService::CACHE_KEY);
    Cache::forget(InstagramFeedService::STALE_CACHE_KEY);

    $feed = app(InstagramFeedService::class)->getCachedOrFallback(5);

    expect($feed['source'])->toBe('curated')
        ->and($feed['posts'])->not->toBeEmpty();

    foreach ($feed['posts'] as $post) {
        expect($post['likes'])->toBeNull()
            ->and($post['comments'])->toBeNull()
            ->and($post['timestamp_human'])->toBeNull()
            ->and($post['image'])->not->toContain('.jpg');
    }
});

test('legacy crowd_patterns and passkeys tables plus 2fa columns are removed (P0-05, P2-01)', function () {
    expect(Schema::hasTable('crowd_patterns'))->toBeFalse()
        ->and(Schema::hasTable('passkeys'))->toBeFalse()
        ->and(Schema::hasColumns('users', [
            'two_factor_secret',
            'two_factor_recovery_codes',
            'two_factor_confirmed_at',
        ]))->toBeFalse();
});

test('active page_sections for home and cerita are synchronized with rendered views (P0-06)', function () {
    $this->seed(Redesign2026Seeder::class);

    $homeKeys = PageSection::where('page', 'home')
        ->where('is_active', true)
        ->orderBy('sort')
        ->pluck('key')
        ->all();

    expect($homeKeys)->toBe([
        'hero',
        'promo_banner',
        'marquee',
        'signature_reel',
        'karcis_teaser',
        'gallery_diagonal',
        'faq',
        'testimonials',
        'instagram_feed',
        'cta_footer',
    ])->not->toContain('rules');

    $ceritaKeys = PageSection::where('page', 'cerita')
        ->where('is_active', true)
        ->orderBy('sort')
        ->pluck('key')
        ->all();

    expect($ceritaKeys)->not->toContain('altimeter')
        ->toContain('hero', 'history', 'founder', 'values', 'facilities', 'stalls', 'gallery_preview', 'collab');
});

test('settings composite (group, key) isolates identical keys across groups (P2-04)', function () {
    Settings::set('group_a.notes', 'Catatan Grup A');
    Settings::set('group_b.notes', 'Catatan Grup B');

    expect(Settings::get('group_a.notes'))->toBe('Catatan Grup A')
        ->and(Settings::get('group_b.notes'))->toBe('Catatan Grup B');
});

test('EnforcePasswordChange middleware forces profile update until password is changed (P0-07)', function () {
    $owner = User::factory()->create([
        'role' => 'owner',
        'must_change_password' => true,
    ]);

    $this->actingAs($owner)
        ->get('/admin')
        ->assertRedirect('/admin/profile');

    $this->actingAs($owner)
        ->get('/admin/profile')
        ->assertOk();

    $owner->update(['password' => 'BaruAman12345']);
    $owner->refresh();

    expect($owner->must_change_password)->toBeFalse();

    $this->flushSession();

    $this->actingAs($owner)
        ->get('/admin')
        ->assertOk();
});

test('sitemap.xml lists public pages and active menus while starter kit routes stay closed (P3-02, P2-01)', function () {
    $category = MenuCategory::create([
        'name' => 'Signature',
        'slug' => 'signature',
        'sort' => 1,
        'is_active' => true,
    ]);

    MenuItem::create([
        'category_id' => $category->id,
        'name' => 'Kopi Gula Aren',
        'slug' => 'kopi-gula-aren-test',
        'price' => 25000,
        'is_active' => true,
        'is_available' => true,
    ]);

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=utf-8')
        ->assertSee(url('/menu/kopi-gula-aren-test'), false)
        ->assertSee(url('/reservasi'), false);

    $this->get('/register')->assertNotFound();
    $this->get('/forgot-password')->assertNotFound();
    $this->get('/.well-known/passkey-endpoints')->assertNotFound();
});

test('prune proofs artisan command succeeds cleanly (P1-05)', function () {
    $this->artisan('reservations:prune-proofs')
        ->assertExitCode(0);
});

test('settings batch save refreshes memory cache and persists values cleanly', function () {
    Settings::setMany([
        'brand.brand_short' => 'UMCO TEST',
        'brand.today_note' => 'Catatan hero saja',
    ]);

    expect(Settings::get('brand.brand_short'))->toBe('UMCO TEST')
        ->and(Settings::get('brand.today_note'))->toBe('Catatan hero saja');
});

test('menu item image upload clears stale variants and placeholder flag', function () {
    $category = MenuCategory::create([
        'name' => 'Signature',
        'slug' => 'signature-img-test',
        'is_active' => true,
    ]);

    $item = MenuItem::create([
        'category_id' => $category->id,
        'name' => 'Kopi Test Upload',
        'slug' => 'kopi-test-upload',
        'price' => 28000,
        'image_path' => '/images/menu/old.webp',
        'image_variants' => ['webp' => ['800' => 'menu/old-800.webp']],
        'is_placeholder' => true,
        'is_active' => true,
        'is_available' => true,
    ]);

    $item->update(['image_path' => 'menu/foto-asli.webp']);
    $item->refresh();

    expect($item->is_placeholder)->toBeFalse()
        ->and($item->image_variants)->toBeNull()
        ->and($item->imageUrlFor())->toContain('menu/foto-asli.webp');
});

test('reservation page removes tmi notes and places min spend inside party card', function () {
    Settings::set('brand.today_note', 'Catatan harian rahasia');
    Settings::set('reservation.min_party', 2);

    $this->get('/reservasi')
        ->assertOk()
        ->assertDontSee('Catatan harian rahasia')
        ->assertDontSee('Minimal 2 orang, maksimal')
        ->assertSee('Minimal belanja:');
});

test('backoffice dashboard, reservations table, and settings page render without icon errors', function () {
    $owner = User::where('email', 'owner@umco.house')->first();
    if (! $owner) {
        $owner = User::create([
            'name' => 'Owner Uji',
            'email' => 'owner-ui@umco.house',
            'password' => 'PasswordKuat#2026!',
            'role' => 'owner',
            'must_change_password' => false,
        ]);
    } else {
        $owner->forceFill(['must_change_password' => false])->save();
    }

    $this->actingAs($owner)
        ->get('/admin')
        ->assertOk()
        ->assertSee('Tren Reservasi');

    $this->actingAs($owner)
        ->get('/admin/reservations')
        ->assertOk();

    $this->actingAs($owner)
        ->get('/admin/manage-settings')
        ->assertOk();
});

test('instagram service fetches and normalizes sociablekit feed with 1h cache and respects show_feed=false', function () {
    Cache::forget(InstagramFeedService::CACHE_KEY);
    Cache::forget(InstagramFeedService::STALE_CACHE_KEY);
    Settings::set('contact.instagram_widget_id', '99988877');

    Http::fake([
        'https://data.accentapi.com/feed/99988877.json' => Http::sequence()
            ->push([
                'user_info' => ['show_feed' => true],
                'bio' => [
                    'username' => 'um.co_house',
                    'full_name' => 'UMCO House',
                    'followers_count' => 1450,
                    'posts_count' => 42,
                    'link' => 'https://www.instagram.com/um.co_house',
                ],
                'posts' => array_map(fn (int $i) => [
                    'id' => 'sk-'.$i,
                    'link' => "https://www.instagram.com/p/sk-{$i}/",
                    'image_url' => "https://cdn.example.com/p{$i}.webp",
                    'pic_text' => "Seduhan {$i}",
                    'pic_like_count_formatted' => 12 + $i,
                    'pic_comment_count_formatted' => $i,
                    'pic_type' => $i === 1 ? 'Video' : 'Image',
                ], range(1, 10)),
            ])
            ->push([
                'user_info' => ['show_feed' => false],
                'posts' => [['id' => '1', 'image_url' => 'https://cdn.example.com/1.webp']],
            ]),
    ]);

    $feed = app(InstagramFeedService::class)->getFeed(8, true);

    expect($feed['source'])->toBe('sociablekit')
        ->and($feed['hidden'])->toBeFalse()
        ->and($feed['bio']['followers_count'])->toBe(1450)
        ->and($feed['posts'])->toHaveCount(8)
        ->and($feed['posts'][0]['is_video'])->toBeTrue()
        ->and($feed['posts'][0]['likes'])->toBe(13);

    Cache::forget(InstagramFeedService::CACHE_KEY);
    Cache::forget(InstagramFeedService::STALE_CACHE_KEY);

    $hiddenFeed = app(InstagramFeedService::class)->getFeed(8, true);

    expect($hiddenFeed['hidden'])->toBeTrue()
        ->and($hiddenFeed['posts'])->toBeEmpty();
});

test('public homepage replaces scroll meter with floating nota and uses cafe terminology', function () {
    $this->get('/')
        ->assertOk()
        ->assertDontSee('scroll-meter', false)
        ->assertSee('umco:nota-updated', false)
        ->assertDontSee('kedai', false);
});
