<?php

use App\Models\User;

test('legacy starter kit settings security routes are closed', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)->get('/settings/security')->assertNotFound();
    $this->actingAs($user)->get('/settings/appearance')->assertNotFound();
    $this->actingAs($user)->get('/settings/password')->assertNotFound();
    $this->get('/.well-known/passkey-endpoints')->assertNotFound();
});

test('public and admin responses enforce hardening security headers', function () {
    $home = $this->get('/');
    $home->assertOk();
    $home->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    $home->assertHeader('X-Content-Type-Options', 'nosniff');
    $home->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

    $login = $this->get('/admin/login');
    $login->assertOk();
    $login->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    $login->assertHeader('X-Content-Type-Options', 'nosniff');
});
