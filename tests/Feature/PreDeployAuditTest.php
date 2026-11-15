<?php

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\PageSection;
use App\Models\User;
use App\Services\InstagramFeedService;
use App\Support\Settings;
use Database\Seeders\Redesign2026Seeder;
use Illuminate\Support\Facades\Cache;
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
