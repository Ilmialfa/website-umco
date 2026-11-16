<?php

use App\Models\User;

test('legacy dashboard redirects unauthenticated and staff users to admin panel', function () {
    $this->get('/dashboard')->assertRedirect('/admin');
    $this->get('/admin')->assertRedirect('/admin/login');

    $staff = User::factory()->create(['role' => 'admin']);
    $this->actingAs($staff)
        ->get('/dashboard')
        ->assertRedirect('/admin');
});

test('staff roles can visit admin dashboard while non-staff roles receive forbidden', function () {
    foreach (['owner', 'admin'] as $role) {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }

    foreach (['guest', 'cashier'] as $blockedRole) {
        $blocked = User::factory()->create(['role' => $blockedRole]);

        $this->actingAs($blocked)
            ->get('/admin')
            ->assertForbidden();
    }
});
