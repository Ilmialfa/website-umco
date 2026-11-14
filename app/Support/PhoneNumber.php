<?php

namespace App\Support;

use InvalidArgumentException;

class PhoneNumber
{
    /**
     * Normalizes an Indonesian phone number to international WhatsApp format (e.g. 628xxxxxxxxxx).
     *
     * Rules:
     * 1. Remove all non-digit characters.
     * 2. Handle prefixes:
     *    - '6208...' -> '628...' (common typo from +62 08...)
     *    - '628...' -> keep as is
     *    - '08...' -> replace '0' with '62' -> '628...'
     *    - '8...' -> prepend '62' -> '628...'
     * 3. Valid result must start with '628' and have length between 10 and 15 digits.
     */
    public static function normalize(?string $phone): string
    {
        if (empty($phone)) {
            throw new InvalidArgumentException('Nomor WhatsApp belum benar, contoh: 0812 3456 7890');
        }

        // 1. Strip all non-digit characters
        $digits = preg_replace('/\D+/', '', $phone);

        if (empty($digits)) {
            throw new InvalidArgumentException('Nomor WhatsApp belum benar, contoh: 0812 3456 7890');
        }

        // 2. Handle prefixes
        if (str_starts_with($digits, '6208')) {
            // e.g. +62 0822... -> 620822... => 62822...
            $digits = '62' . substr($digits, 3);
        } elseif (str_starts_with($digits, '628')) {
            // Already standard format
            $digits = $digits;
        } elseif (str_starts_with($digits, '08')) {
            // Local 08... format -> replace 0 with 62
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            // Missing leading zero -> prepend 62
            $digits = '62' . $digits;
        }

        // 3. Validation
        if (!self::isValidNormalized($digits)) {
            throw new InvalidArgumentException('Nomor WhatsApp belum benar, contoh: 0812 3456 7890');
        }

        return $digits;
    }

    /**
     * Checks if a raw phone string can be normalized to a valid WhatsApp number.
     */
    public static function isValid(?string $phone): bool
    {
        try {
            self::normalize($phone);
            return true;
        } catch (InvalidArgumentException) {
            return false;
        }
    }

    /**
     * Internal check for a normalized digit string.
     */
    public static function isValidNormalized(string $digits): bool
    {
        return str_starts_with($digits, '628')
            && strlen($digits) >= 10
            && strlen($digits) <= 15;
    }

    /**
     * Normalizes an Indonesian business/landline OR mobile phone number (e.g. 02248001571 -> 622248001571).
     * Dedicated for business contact numbers; does not relax customer mobile validation in normalize().
     */
    public static function normalizeBusiness(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            throw new InvalidArgumentException('Nomor WhatsApp bisnis tidak boleh kosong.');
        }

        if (str_starts_with($digits, '620')) {
            $digits = '62' . substr($digits, 3);
        } elseif (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (! str_starts_with($digits, '62')) {
            $digits = '62' . $digits;
        }

        if (! str_starts_with($digits, '62') || strlen($digits) < 9 || strlen($digits) > 15) {
            throw new InvalidArgumentException('Format nomor bisnis tidak valid.');
        }

        return $digits;
    }

    /**
     * Formats normalized or raw phone to human-readable format: +62 822-4800-1571.
     */
    public static function format(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        try {
            $normalized = self::normalize($phone);
        } catch (InvalidArgumentException) {
            return $phone;
        }

        // e.g. 6282248001571 -> +62 822-4800-1571
        $rest = substr($normalized, 2); // 82248001571
        if (strlen($rest) >= 10) {
            $prefix = substr($rest, 0, 3); // 822
            $middle = substr($rest, 3, 4); // 4800
            $end = substr($rest, 7);      // 1571
            return "+62 {$prefix}-{$middle}-{$end}";
        }

        return "+{$normalized}";
    }

    /**
     * Generates a direct WhatsApp click-to-chat URL with optional pre-filled message.
     */
    public static function waLink(?string $phone, string $message = ''): string
    {
        try {
            $normalized = self::normalize($phone);
        } catch (InvalidArgumentException) {
            $normalized = preg_replace('/\D+/', '', $phone ?? '');
        }

        $url = "https://wa.me/{$normalized}";
        if (!empty($message)) {
            $url .= '?text=' . rawurlencode($message);
        }

        return $url;
    }
}
