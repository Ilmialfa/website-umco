<?php

test('starter kit two factor challenge endpoint is disabled', function () {
    $this->get('/two-factor-challenge')->assertNotFound();
});
