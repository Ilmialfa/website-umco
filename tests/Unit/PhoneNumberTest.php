<?php

use App\Support\PhoneNumber;

test('normalizes all Indonesian phone formats to 6282248001571', function (string $input) {
    expect(PhoneNumber::normalize($input))->toBe('6282248001571');
})->with([
    '082248001571',
    '6282248001571',
    '+62 822-4800-1571',
    '+6282248001571',
    '82248001571',
    '0822 4800 1571',
    '+62 0822 4800 1571',
]);

test('validates numbers properly', function () {
    expect(PhoneNumber::isValid('082248001571'))->toBeTrue()
        ->and(PhoneNumber::isValid('+62 822-4800-1571'))->toBeTrue()
        ->and(PhoneNumber::isValid('82248001571'))->toBeTrue()
        ->and(PhoneNumber::isValid('+62 0822 4800 1571'))->toBeTrue()
        ->and(PhoneNumber::isValid('12345'))->toBeFalse()
        ->and(PhoneNumber::isValid('021888999'))->toBeFalse() // Landline not 628
        ->and(PhoneNumber::isValid(''))->toBeFalse()
        ->and(PhoneNumber::isValid('abcd'))->toBeFalse();
});

test('throws exception on invalid phone number', function () {
    PhoneNumber::normalize('021123456');
})->throws(InvalidArgumentException::class, 'Nomor WhatsApp belum benar, contoh: 0812 3456 7890');

test('formats phone to human-readable format', function () {
    expect(PhoneNumber::format('082248001571'))->toBe('+62 822-4800-1571');
});

test('generates valid WhatsApp click to chat link', function () {
    $link = PhoneNumber::waLink('082248001571', 'Halo UM.CO House!');
    expect($link)->toBe('https://wa.me/6282248001571?text=Halo%20UM.CO%20House%21');
});
