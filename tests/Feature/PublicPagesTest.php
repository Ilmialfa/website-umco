<?php

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Reservation;

it('renders home page without legacy copy, emojis, or external fonts', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('UMCO');
    foreach (['hero', 'marquee', 'hari-ini', 'menu-nota', 'signature-reel', 'story-line', 'seduh', 'jam-ramai', 'karcis-teaser', 'aturan', 'kata-mereka', 'galeri', 'lokasi'] as $sectionId) {
        $response->assertSee("id=\"{$sectionId}\"", false);
    }
    $response->assertDontSee('Wing Barat');
    $response->assertDontSee('Wing Timur');
    $response->assertDontSee('huruf H');
    $response->assertDontSee('fonts.googleapis.com');
    $response->assertDontSee('fonts.bunny.net');
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
    expect($response->getContent())->not->toMatch('/[\x{1F300}-\x{1FAFF}]/u');
});

it('does not expose legacy specimen route', function () {
    $this->get('/specimen')->assertNotFound();
});

it('renders reservation page successfully', function () {
    $response = $this->get('/reservasi');
    $response->assertStatus(200);
    $response->assertSee('Pilih Meja');
    $response->assertDontSee('Wing Barat');
});

it('downloads ics calendar event for a valid reservation', function () {
    $reservation = Reservation::create([
        'name' => 'Fulan Test',
        'phone_input' => '081234567890',
        'date' => now()->format('Y-m-d'),
        'time' => '14:00',
        'party_size' => 2,
        'mode' => 'table_only',
    ]);

    $response = $this->get("/reservasi/calendar/{$reservation->code}");
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
    $response->assertSee('BEGIN:VCALENDAR');
    $response->assertSee("UID:{$reservation->code}@umco.house");
    $response->assertSee('UM.CO House');
});
