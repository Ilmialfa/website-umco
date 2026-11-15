<?php

namespace App\Support;

class IndonesianFormat
{
    private const SATUAN = [
        '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
        'sepuluh', 'sebelas',
    ];

    public static function terbilang(int $amount): string
    {
        if ($amount <= 0) {
            return 'nol rupiah';
        }

        return trim(preg_replace('/\s+/', ' ', self::convert($amount))).' rupiah';
    }

    private static function convert(int $n): string
    {
        if ($n < 12) {
            return self::SATUAN[$n];
        }
        if ($n < 20) {
            return self::convert($n - 10).' belas';
        }
        if ($n < 100) {
            return trim(self::convert(intdiv($n, 10)).' puluh '.self::convert($n % 10));
        }
        if ($n < 200) {
            return trim('seratus '.self::convert($n - 100));
        }
        if ($n < 1000) {
            return trim(self::convert(intdiv($n, 100)).' ratus '.self::convert($n % 100));
        }
        if ($n < 2000) {
            return trim('seribu '.self::convert($n - 1000));
        }
        if ($n < 1000000) {
            return trim(self::convert(intdiv($n, 1000)).' ribu '.self::convert($n % 1000));
        }
        if ($n < 1000000000) {
            return trim(self::convert(intdiv($n, 1000000)).' juta '.self::convert($n % 1000000));
        }

        return (string) $n;
    }

    public static function ribu(int $amount): string
    {
        if ($amount >= 1000) {
            $ribu = $amount / 1000;
            $str = fmod($ribu, 1.0) === 0.0
                ? (string) (int) $ribu
                : str_replace('.', ',', (string) $ribu);

            return $str.' ribu';
        }

        return 'Rp '.number_format($amount, 0, ',', '.');
    }

    public static function rupiah(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
