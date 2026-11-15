<?php

test('starter kit password reset endpoints are disabled', function () {
    $this->get('/forgot-password')->assertNotFound();
    $this->post('/forgot-password', ['email' => 'owner@umco.house'])->assertNotFound();
    $this->get('/reset-password/fake-token')->assertNotFound();
});
