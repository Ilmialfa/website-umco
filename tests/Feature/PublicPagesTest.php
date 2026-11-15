<?php

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Reservation;

it('renders home page without legacy copy, emojis, or external fonts', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('UMCO');
    $response->assertSee('UM.CO');
    $response->assertSee('Hubungi Kami');
    $response->assertSee('Reservasi');
    foreach (['hero', 'marquee', 'banner-utama', 'signature-reel', 'menu-nota', 'karcis-teaser', 'galeri', 'galeri-diagonal', 'kata-mereka', 'cta-footer'] as $sectionId) {
        $response->assertSee("id=\"{$sectionId}\"", false);
    }
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
    $this->get('/about')->assertRedirect('/cerita')->assertStatus(301);

    $response = $this->get('/cerita');
    $response->assertStatus(200);
    $response->assertSee('Dari kampus, buat yang lagi butuh tempat.');
    $response->assertSee('Tiga hal yang kami pegang');
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
