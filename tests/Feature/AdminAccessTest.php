<?php

use App\Models\User;
use App\Support\Settings;

test('guests are redirected to admin login', function () {
    $response = $this->get('/admin');
    $response->assertRedirect('/admin/login');
});

test('cashier can access admin panel', function () {
    $cashier = User::factory()->create([
        'role' => 'cashier',
    ]);

    $this->actingAs($cashier)
        ->get('/admin')
        ->assertSuccessful();
});

test('owner can access admin panel and manage settings', function () {
    $owner = User::factory()->create([
        'role' => 'owner',
    ]);

    $this->actingAs($owner)
        ->get('/admin')
        ->assertSuccessful();
});

test('settings helper can update and retrieve values correctly', function () {
    Settings::set('brand.brand_name', 'UM.CO House Kampus UMRI');
    expect(Settings::get('brand.brand_name'))->toBe('UM.CO House Kampus UMRI');
});
