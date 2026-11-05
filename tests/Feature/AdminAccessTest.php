<?php

use App\Models\Reservation;
use App\Models\User;
use App\Support\Settings;

test('guests are redirected to admin login', function () {
    $response = $this->get('/admin');
    $response->assertRedirect('/admin/login');
});

test('cashier can access operational and catalog resources', function () {
    $cashier = User::factory()->create([
        'role' => 'cashier',
    ]);

    $reservation = Reservation::create([
        'name' => 'Tamu Kasir',
        'phone_input' => '081234567891',
        'date' => now()->format('Y-m-d'),
        'time' => '15:00',
        'party_size' => 4,
        'mode' => 'table_only',
        'status' => 'pending',
    ]);

    $routes = [
        '/admin',
        '/admin/reservations',
        "/admin/reservations/{$reservation->id}",
        '/admin/reservation-reports',
        '/admin/closed-dates',
        '/admin/menu-items',
        '/admin/menu-categories',
        '/admin/street-stalls',
        '/admin/banners',
        '/admin/facilities',
        '/admin/testimonials',
        '/admin/story-blocks',
        '/admin/gallery-items',
    ];

    foreach ($routes as $route) {
        $response = $this->actingAs($cashier)->get($route);
        $response->assertSuccessful();
    }
});

test('owner can access all admin resources and pages', function () {
    $owner = User::factory()->create([
        'role' => 'owner',
    ]);

    $reservation = Reservation::create([
        'name' => 'Tamu Test',
        'phone_input' => '081234567890',
        'date' => now()->format('Y-m-d'),
        'time' => '14:00',
        'party_size' => 2,
        'mode' => 'table_only',
        'status' => 'pending',
    ]);

    $routes = [
        '/admin',
        '/admin/reservations',
        "/admin/reservations/{$reservation->id}",
        '/admin/reservation-reports',
        '/admin/closed-dates',
        '/admin/menu-items',
        '/admin/menu-categories',
        '/admin/street-stalls',
        '/admin/banners',
        '/admin/facilities',
        '/admin/testimonials',
        '/admin/story-blocks',
        '/admin/gallery-items',
        '/admin/manage-settings',
        '/admin/users',
        '/admin/activity-logs',
    ];

    foreach ($routes as $route) {
        $response = $this->actingAs($owner)->get($route);
        $response->assertSuccessful();
    }
});

test('owner can access all create pages', function () {
    $owner = User::factory()->create([
        'role' => 'owner',
    ]);

    $createRoutes = [
        '/admin/reservations/create',
        '/admin/menu-items/create',
        '/admin/menu-categories/create',
        '/admin/closed-dates/create',
        '/admin/street-stalls/create',
        '/admin/banners/create',
        '/admin/facilities/create',
        '/admin/testimonials/create',
        '/admin/story-blocks/create',
        '/admin/gallery-items/create',
        '/admin/users/create',
    ];

    foreach ($createRoutes as $route) {
        $response = $this->actingAs($owner)->get($route);
        $response->assertSuccessful();
    }
});

test('cashier and owner can access edit pages for reservations and catalog', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $cashier = User::factory()->create(['role' => 'cashier']);

    $reservation = Reservation::create([
        'name' => 'Tamu Edit Test',
        'phone_input' => '081234567899',
        'date' => now()->format('Y-m-d'),
        'time' => '16:00',
        'party_size' => 2,
        'mode' => 'table_only',
        'status' => 'pending',
    ]);

    $this->actingAs($owner)->get("/admin/reservations/{$reservation->id}/edit")->assertSuccessful();
    $this->actingAs($cashier)->get("/admin/reservations/{$reservation->id}/edit")->assertSuccessful();
    $this->actingAs($cashier)->get('/admin/reservations/create')->assertSuccessful();
});

test('cashier is forbidden from sensitive owner-only settings and user management', function () {
    $cashier = User::factory()->create(['role' => 'cashier']);

    $forbiddenRoutes = [
        '/admin/users',
        '/admin/manage-settings',
        '/admin/activity-logs',
    ];

    foreach ($forbiddenRoutes as $route) {
        $this->actingAs($cashier)->get($route)->assertForbidden();
    }
});
