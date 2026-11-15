<?php

use App\Models\User;

test('starter kit confirm password endpoint is disabled', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get('/user/confirm-password')
        ->assertNotFound();
});
