<?php

use App\Livewire\ReservationPage;
use App\Models\Facility;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Reservation;
use Livewire\Livewire;

it('renders home page without legacy copy, emojis, or external fonts', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('UMCO');
    $response->assertSee('UM.CO');
    $response->assertSee('Hubungi Kami');
    $response->assertSee('Reservasi');
    foreach (['hero', 'banner-utama', 'marquee', 'signature-reel', 'karcis-teaser', 'galeri-diagonal', 'faq', 'kata-mereka', 'instagram-feed', 'lokasi-umco', 'cta-footer'] as $sectionId) {
        $response->assertSee("id=\"{$sectionId}\"", false);
    }
    $content = (string) $response->getContent();
    expect(strpos($content, 'id="lokasi-umco"'))->toBeLessThan(strpos($content, 'id="cta-footer"'));
    $response->assertDontSee('Salin Lat Lng');
    $response->assertDontSee('id="menu-nota"', false);
    $response->assertDontSee('id="galeri"', false);
    $response->assertDontSee('Wing Barat');
    $response->assertDontSee('Wing Timur');
    $response->assertDontSee('huruf H');
    $response->assertDontSee('fonts.googleapis.com');
    $response->assertDontSee('fonts.bunny.net');
    $response->assertDontSee('font-led');
    $response->assertDontSee('Section 01');
    $response->assertDontSee('#biji');
    expect($response->getContent())->not->toMatch('/[\x{1F300}-\x{1FAFF}]/u');
});

it('renders menu page with items and categories', function () {
    $category = MenuCategory::create([
        'name' => 'Signature Coffee Test',
        'slug' => 'signature-coffee-test',
        'sort' => 1,
        'is_active' => true,
    ]);

    MenuItem::create([
        'category_id' => $category->id,
        'name' => 'Kopi Pandan UMCO',
        'slug' => 'kopi-pandan-umco',
        'price' => 28000,
        'is_placeholder' => true,
        'is_active' => true,
        'is_available' => true,
    ]);

    $response = $this->get('/menu');
    $response->assertStatus(200);
    $response->assertSee('Menu');
    $response->assertSee('Kopi Pandan UMCO');
    $response->assertSee('foto menyusul');
    expect($response->getContent())->not->toMatch('/[\x{1F300}-\x{1FAFF}]/u');
});

it('renders cerita page and permanently redirects /about to /cerita', function () {
    Facility::create([
        'label' => 'Wi-Fi Kampus Cepat',
        'icon' => 'wifi',
        'sort' => 1,
        'is_active' => true,
    ]);

    $this->get('/about')->assertRedirect('/cerita')->assertStatus(301);

    $response = $this->get('/cerita');
    $response->assertStatus(200);
    $response->assertSee('Dari kampus, buat yang lagi butuh tempat.');
    $response->assertSee('Sejarah Singkat UMCO');
    $response->assertSee('Di Balik UMCO');
    $response->assertSee('Ruang &amp; Fasilitas', false);
    $response->assertSee('Kolaborasi &amp; Reservasi Rombongan', false);
    $response->assertSee('Tiga hal yang kami pegang');
    $response->assertSee('id="lokasi-umco"', false);
    $response->assertDontSee('Salin Lat Lng');
    $response->assertDontSee('Wing Barat');
    $response->assertDontSee('Arsitektur');
    $response->assertDontSee('id="biji"', false);
    $response->assertDontSee('mdpl');
    expect($response->getContent())->not->toMatch('/[\x{1F300}-\x{1FAFF}]/u');
});

it('does not expose legacy specimen route', function () {
    $this->get('/specimen')->assertNotFound();
});

it('renders reservation page successfully', function () {
    $response = $this->get('/reservasi');
    $response->assertStatus(200);
    $response->assertSee('Pesan meja di UMCO.');
    $response->assertDontSee('Wing Barat');
});

it('downloads ics calendar event for a valid reservation', function () {
    $reservation = Reservation::create([
        'name' => 'Fulan Test',
        'phone_input' => '081234567890',
        'date' => now()->format('Y-m-d'),
        'time' => '14:00',
        'party_size' => 2,
        'mode' => 'table_menu',
    ]);

    $response = $this->get("/reservasi/calendar/{$reservation->code}");
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
    $response->assertSee('BEGIN:VCALENDAR');
    $response->assertSee("UID:{$reservation->code}@umco.house");
    $response->assertSee('UMCO House');
    $response->assertSee('Tamu UMCO');
    $response->assertDontSee('Fulan Test');

    $verifiedResponse = $this->withSession(["umco_status_verified_{$reservation->code}" => true])
        ->get("/reservasi/calendar/{$reservation->code}");
    $verifiedResponse->assertStatus(200)->assertSee('Fulan Test');
});

it('renders menu detail page with description, pricing, and reservation CTA', function () {
    $category = MenuCategory::create([
        'name' => 'Kopi Susu Detail',
        'slug' => 'kopi-susu-detail',
        'sort' => 1,
        'is_active' => true,
    ]);

    MenuItem::create([
        'category_id' => $category->id,
        'name' => 'Aren Coffee',
        'slug' => 'aren-coffee',
        'price' => 24000,
        'description' => 'Perpaduan gula aren pilihan dengan susu segar dan double espresso.',
        'ingredients' => 'Espresso, gula aren, susu segar',
        'has_temperature' => true,
        'is_active' => true,
        'is_available' => true,
    ]);

    $response = $this->get('/menu/aren-coffee');
    $response->assertStatus(200);
    $response->assertSee('Aren Coffee');
    $response->assertSee('Perpaduan gula aren pilihan');
    $response->assertSee('Lanjut Reservasi');
    $response->assertDontSee('Wing Barat');
});

it('imports nota items on ReservationPage without Livewire lifecycle hook collision', function () {
    $category = MenuCategory::create([
        'name' => 'Kopi Hitam Nota',
        'slug' => 'kopi-hitam-nota',
        'sort' => 1,
        'is_active' => true,
    ]);

    $item = MenuItem::create([
        'category_id' => $category->id,
        'name' => 'Americano Test',
        'slug' => 'americano-test',
        'price' => 24000,
        'is_active' => true,
        'is_available' => true,
    ]);

    $component = Livewire::test(ReservationPage::class)
        ->call('importNotaItems', [
            ['slug' => 'americano-test', 'qty' => 2, 'variant_label' => null, 'temperature' => 'Es'],
        ]);

    expect($component->instance()->selectedItems[$item->id.'||Es']['qty'])->toBe(2);
});

it('renders galeri page cleanly with filters and cta', function () {
    $response = $this->get('/galeri');
    $response->assertStatus(200);
    $response->assertSee('Galeri');
    $response->assertSee('Semua');
    $response->assertSee('Pesan meja');
    $response->assertSee('Lihat daftar menu');
    $response->assertDontSee('Wing Barat');
    expect($response->getContent())->not->toMatch('/[\x{1F300}-\x{1FAFF}]/u');
});

it('serves instagram feed endpoint for @um.co_house', function () {
    $response = $this->getJson('/instagram-feed');
    $response->assertStatus(200)
        ->assertJsonPath('username', 'um.co_house')
        ->assertJsonStructure(['username', 'profile_url', 'source', 'posts']);
});
