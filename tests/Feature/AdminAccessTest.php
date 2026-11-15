<?php

use App\Actions\Reservations\CreateReservation as CreateReservationAction;
use App\Filament\Pages\CrowdPatternPage;
use App\Filament\Pages\ManageSettings;
use App\Filament\Pages\ReservationReports;
use App\Filament\Resources\ActivityLogs\ActivityLogResource;
use App\Filament\Resources\Banners\BannerResource;
use App\Filament\Resources\BeanOrigins\BeanOriginResource;
use App\Filament\Resources\ClosedDates\ClosedDateResource;
use App\Filament\Resources\Facilities\FacilityResource;
use App\Filament\Resources\GalleryItems\GalleryItemResource;
use App\Filament\Resources\MenuCategories\MenuCategoryResource;
use App\Filament\Resources\MenuItems\MenuItemResource;
use App\Filament\Resources\PageSections\PageSectionResource;
use App\Filament\Resources\Reservations\ReservationResource;
use App\Filament\Resources\Rules\RuleResource;
use App\Filament\Resources\SeatingOptions\SeatingOptionResource;
use App\Filament\Resources\SpendTiers\SpendTierResource;
use App\Filament\Resources\StoryBlocks\StoryBlockResource;
use App\Filament\Resources\StreetStalls\StreetStallResource;
use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\ActivityLog;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Reservation;
use App\Models\SpendTier;
use App\Models\User;
use App\Support\Settings;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

test('enforces Section 12.5 Filament permission matrix across owner, admin, and cashier', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $admin = User::factory()->create(['role' => 'admin']);
    $cashier = User::factory()->create(['role' => 'cashier']);

    $cat = MenuCategory::create(['name' => 'Kopi', 'slug' => 'kopi', 'is_active' => true]);
    $menu = MenuItem::create(['category_id' => $cat->id, 'name' => 'V60', 'slug' => 'v60', 'price' => 30000, 'is_active' => true]);

    // OWNER: penuh atas semua halaman dan resource
    $this->actingAs($owner);
    expect(ReservationResource::canAccess())->toBeTrue()
        ->and(MenuItemResource::canAccess())->toBeTrue()
        ->and(MenuItemResource::canCreate())->toBeTrue()
        ->and(MenuItemResource::canEdit($menu))->toBeTrue()
        ->and(MenuCategoryResource::canAccess())->toBeTrue()
        ->and(ClosedDateResource::canAccess())->toBeTrue()
        ->and(ReservationReports::canAccess())->toBeTrue()
        ->and(PageSectionResource::canAccess())->toBeTrue()
        ->and(RuleResource::canAccess())->toBeTrue()
        ->and(SpendTierResource::canAccess())->toBeTrue()
        ->and(SeatingOptionResource::canAccess())->toBeTrue()
        ->and(BeanOriginResource::canAccess())->toBeTrue()
        ->and(CrowdPatternPage::canAccess())->toBeTrue()
        ->and(BannerResource::canAccess())->toBeTrue()
        ->and(StoryBlockResource::canAccess())->toBeTrue()
        ->and(GalleryItemResource::canAccess())->toBeTrue()
        ->and(StreetStallResource::canAccess())->toBeTrue()
        ->and(TestimonialResource::canAccess())->toBeTrue()
        ->and(FacilityResource::canAccess())->toBeTrue()
        ->and(ManageSettings::canAccess())->toBeTrue()
        ->and(UserResource::canAccess())->toBeTrue()
        ->and(ActivityLogResource::canAccess())->toBeTrue();

    // ADMIN: hanya operasional & menu, TIDAK bisa ubah CMS/aturan/settings/pengguna/audit
    $this->actingAs($admin);
    expect(ReservationResource::canAccess())->toBeTrue()
        ->and(MenuItemResource::canAccess())->toBeTrue()
        ->and(MenuItemResource::canCreate())->toBeTrue()
        ->and(MenuItemResource::canEdit($menu))->toBeTrue()
        ->and(MenuCategoryResource::canAccess())->toBeTrue()
        ->and(ClosedDateResource::canAccess())->toBeTrue()
        ->and(ReservationReports::canAccess())->toBeTrue()
        ->and(PageSectionResource::canAccess())->toBeFalse()
        ->and(RuleResource::canAccess())->toBeFalse()
        ->and(SpendTierResource::canAccess())->toBeFalse()
        ->and(SeatingOptionResource::canAccess())->toBeFalse()
        ->and(BeanOriginResource::canAccess())->toBeFalse()
        ->and(CrowdPatternPage::canAccess())->toBeFalse()
        ->and(BannerResource::canAccess())->toBeFalse()
        ->and(StoryBlockResource::canAccess())->toBeFalse()
        ->and(GalleryItemResource::canAccess())->toBeFalse()
        ->and(StreetStallResource::canAccess())->toBeFalse()
        ->and(TestimonialResource::canAccess())->toBeFalse()
        ->and(FacilityResource::canAccess())->toBeFalse()
        ->and(ManageSettings::canAccess())->toBeFalse()
        ->and(UserResource::canAccess())->toBeFalse()
        ->and(ActivityLogResource::canAccess())->toBeFalse();

    // CASHIER: reservasi & lihat/ubah status habis menu, TIDAK bisa buat/ubah harga/foto menu, CMS, settings, audit
    $this->actingAs($cashier);
    expect(ReservationResource::canAccess())->toBeTrue()
        ->and(MenuItemResource::canAccess())->toBeTrue()
        ->and(MenuItemResource::canCreate())->toBeFalse()
        ->and(MenuItemResource::canEdit($menu))->toBeFalse()
        ->and(MenuItemResource::canDelete($menu))->toBeFalse()
        ->and(MenuCategoryResource::canAccess())->toBeFalse()
        ->and(ClosedDateResource::canAccess())->toBeFalse()
        ->and(ReservationReports::canAccess())->toBeFalse()
        ->and(ManageSettings::canAccess())->toBeFalse()
        ->and(UserResource::canAccess())->toBeFalse()
        ->and(ActivityLogResource::canAccess())->toBeFalse()
        ->and(PageSectionResource::canAccess())->toBeFalse();

    $this->actingAs($admin)->get('/admin/manage-settings')->assertForbidden();
    $this->actingAs($admin)->get('/admin/page-sections')->assertForbidden();
    $this->actingAs($cashier)->get('/admin/manage-settings')->assertForbidden();
    $this->actingAs($owner)->get('/admin/manage-settings')->assertOk();
});

test('admin walk-in cash and owner skip_min_rules go through shared CreateReservation action and log ActivityLog', function () {
    Notification::fake();
    Bus::fake();

    SpendTier::create(['party_min' => 2, 'party_max' => 2, 'min_spend' => 50000]);
    Settings::set('hours.opening_time', '08:00', 'hours');
    Settings::set('hours.closing_time', '22:00', 'hours');
    Settings::flush();

    $owner = User::factory()->create(['role' => 'owner']);
    $admin = User::factory()->create(['role' => 'admin']);
    $cat = MenuCategory::create(['name' => 'Makanan', 'slug' => 'makanan', 'is_active' => true]);
    $item = MenuItem::create(['category_id' => $cat->id, 'name' => 'Mie Lava UMCO', 'slug' => 'mie-lava', 'price' => 22000, 'is_active' => true]);

    $tomorrow = now('Asia/Jakarta')->addDay()->format('Y-m-d');

    // Admin dilarang memakai skip_min_rules
    expect(fn () => CreateReservationAction::execute([
        'source' => 'walk_in',
        'date' => $tomorrow,
        'time' => '13:00',
        'party_size' => 1,
        'skip_min_rules' => true,
        'name' => 'Tamu Langsung',
        'phone_input' => '081234567890',
        'payment_type' => 'cash',
        'items' => [['menu_item_id' => $item->id, 'qty' => 1]],
    ], null, $admin))->toThrow(ValidationException::class);

    // Owner bisa memakai skip_min_rules dan tunai kasir -> langsung confirmed + terlog
    $res = CreateReservationAction::execute([
        'source' => 'walk_in',
        'date' => $tomorrow,
        'time' => '13:00',
        'party_size' => 1,
        'skip_min_rules' => true,
        'name' => 'Tamu VIP',
        'phone_input' => '081234567890',
        'payment_type' => 'cash',
        'items' => [['menu_item_id' => $item->id, 'qty' => 1]],
    ], null, $owner);

    expect($res->status)->toBe('confirmed')
        ->and($res->payment_status)->toBe('verified')
        ->and($res->source)->toBe('walk_in')
        ->and(ActivityLog::where('action', 'reservation.created')->where('subject_id', $res->id)->exists())->toBeTrue();
});

test('logged WhatsApp redirect route records ActivityLog and redirects to wa.me', function () {
    $cashier = User::factory()->create(['role' => 'cashier']);
    $reservation = Reservation::create([
        'name' => 'Dinda Kirana',
        'phone_input' => '082248001571',
        'date' => now('Asia/Jakarta')->addDay()->format('Y-m-d'),
        'time' => '15:00',
        'party_size' => 3,
        'subtotal' => 100000,
        'dp_amount' => 50000,
        'amount_due' => 50000,
    ]);

    $response = $this->actingAs($cashier)
        ->get(route('admin.wa.redirect', ['reservation' => $reservation->id, 'template' => 'reminder']));

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toStartWith('https://wa.me/6282248001571?text=');
    expect(ActivityLog::where('action', 'reservation.whatsapp_opened')
        ->where('subject_id', $reservation->id)
        ->exists())->toBeTrue();
});

test('enforces policy boundaries, owner-only deletion, last-owner protection, and hides BeanOriginResource nav', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $admin = User::factory()->create(['role' => 'admin']);
    $cashier = User::factory()->create(['role' => 'cashier']);

    $reservation = Reservation::create([
        'name' => 'Rina Test',
        'phone_input' => '081234567890',
        'date' => now('Asia/Jakarta')->addDay()->format('Y-m-d'),
        'time' => '15:00',
        'party_size' => 2,
    ]);

    expect(BeanOriginResource::shouldRegisterNavigation())->toBeFalse();

    $this->actingAs($cashier);
    expect(ReservationResource::canDelete($reservation))->toBeFalse()
        ->and(ReservationResource::canDeleteAny())->toBeFalse()
        ->and($cashier->can('delete', $reservation))->toBeFalse();

    $this->actingAs($admin);
    expect(ReservationResource::canDelete($reservation))->toBeFalse()
        ->and($admin->can('delete', $reservation))->toBeFalse();

    $this->actingAs($owner);
    expect(ReservationResource::canDelete($reservation))->toBeTrue()
        ->and($owner->can('delete', $reservation))->toBeTrue()
        ->and($owner->can('delete', $owner))->toBeFalse()
        ->and($owner->can('delete', $cashier))->toBeTrue();
});

test('sends HTTP security headers on public responses', function () {
    $response = $this->get('/');
    $response->assertStatus(200)
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
});
