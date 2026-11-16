<?php

use App\Filament\Pages\Auth\AdminLogin;
use App\Models\User;
use Livewire\Livewire;

test('legacy login redirects to unified admin login screen', function () {
    $this->get('/login')
        ->assertRedirect('/admin/login');
});

test('admin login screen renders with UMCO branding', function () {
    $this->get('/admin/login')
        ->assertOk()
        ->assertSee('Masuk Dapur Admin')
        ->assertSee('UMCO House');
});

test('staff users can authenticate via admin login screen', function () {
    $staff = User::factory()->create([
        'role' => 'owner',
        'password' => 'SecretPass#2026',
    ]);

    Livewire::test(AdminLogin::class)
        ->fillForm([
            'email' => $staff->email,
            'password' => 'SecretPass#2026',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect('/admin');

    $this->assertAuthenticatedAs($staff);
});

test('users cannot authenticate with invalid password or guest role', function () {
    $staff = User::factory()->create([
        'role' => 'admin',
        'password' => 'SecretPass#2026',
    ]);

    Livewire::test(AdminLogin::class)
        ->fillForm([
            'email' => $staff->email,
            'password' => 'wrong-password',
        ])
        ->call('authenticate')
        ->assertHasFormErrors(['email']);

    $this->assertGuest();

    $guest = User::factory()->create([
        'role' => 'guest',
        'password' => 'SecretPass#2026',
    ]);

    Livewire::test(AdminLogin::class)
        ->fillForm([
            'email' => $guest->email,
            'password' => 'SecretPass#2026',
        ])
        ->call('authenticate')
        ->assertHasFormErrors(['email']);

    $this->assertGuest();
});

test('authenticated staff users can logout from admin panel', function () {
    $staff = User::factory()->create(['role' => 'admin']);

    $this->actingAs($staff)
        ->post('/admin/logout')
        ->assertRedirect('/admin/login');

    $this->assertGuest();
});
