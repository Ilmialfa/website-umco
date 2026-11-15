<?php

test('public self-registration endpoints are disabled', function () {
    $this->get('/register')->assertNotFound();
    $this->post('/register', [
        'name' => 'Intruder',
        'email' => 'intruder@example.com',
        'password' => 'Password#1234',
        'password_confirmation' => 'Password#1234',
    ])->assertNotFound();
});
