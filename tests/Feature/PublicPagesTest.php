<?php

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Reservation;

it('renders home page successfully', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('UM.CO');
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
        'is_active' => true,
        'is_available' => true,
    ]);

    $response = $this->get('/menu');
    $response->assertStatus(200);
    $response->assertSee('Katalog');
    $response->assertSee('Kopi Pandan UMCO');
});

it('renders about page with architectural story and location', function () {
    $response = $this->get('/about');
    $response->assertStatus(200);
    $response->assertSee('Arsitektur');
    $response->assertSee('Wing Barat');
    $response->assertSee('Peta Interaktif');
});

it('renders reservation page successfully', function () {
    $response = $this->get('/reservasi');
    $response->assertStatus(200);
    $response->assertSee('Mode A');
    $response->assertSee('Mode B');
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
