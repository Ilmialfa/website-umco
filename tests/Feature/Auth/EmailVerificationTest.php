<?php

use App\Models\User;

test('starter kit email verification endpoints are disabled', function () {
    $user = User::factory()->unverified()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get('/email/verify')
        ->assertNotFound();
});
