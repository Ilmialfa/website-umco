<?php

test('public self-registration endpoint is disabled for backoffice security', function () {
    $this->get('/register')->assertNotFound();
    $this->post('/register', [
        'name' => 'Attacker',
        'email' => 'attacker@example.com',
        'password' => 'SecretPass123!',
        'password_confirmation' => 'SecretPass123!',
    ])->assertNotFound();
});
