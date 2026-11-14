<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class MenuImageProcessor
{
    public const WIDTHS = [480, 800, 1200];

    /**
     * Konversi foto sumber ke WebP (dan AVIF bila GD mendukung) ukuran 480, 800, 1200 di disk public.
     *
     * @return array{image_path: string, image_variants: array<string, array<string, string>>}
     */
    public static function process(string $sourcePath, string $slug, bool $force = false): array
    {
        $disk = Storage::disk('public');
        $disk->makeDirectory('menu');

        $variants = ['webp' => [], 'avif' => []];
        $hasAvif = function_exists('imageavif');
        $sourceMtime = file_exists($sourcePath) ? (int) filemtime($sourcePath) : 0;

        $allTargetsExist = true;
        foreach (self::WIDTHS as $width) {
            $webpRel = "menu/{$slug}-{$width}.webp";
            $webpAbs = $disk->path($webpRel);
            if (! file_exists($webpAbs) || ($sourceMtime && filemtime($webpAbs) < $sourceMtime)) {
                $allTargetsExist = false;
            }
            $variants['webp'][(string) $width] = $webpRel;

            if ($hasAvif) {
                $avifRel = "menu/{$slug}-{$width}.avif";
                $avifAbs = $disk->path($avifRel);
                if (! file_exists($avifAbs) || ($sourceMtime && filemtime($avifAbs) < $sourceMtime)) {
                    $allTargetsExist = false;
                }
                $variants['avif'][(string) $width] = $avifRel;
            }
        }

        if (! $force && $allTargetsExist) {
            return [
                'image_path' => $variants['webp']['800'],
                'image_variants' => $variants,
            ];
        }

        if (! file_exists($sourcePath)) {
            return [
                'image_path' => '',
                'image_variants' => [],
            ];
        }

        $image = self::loadOrientedImage($sourcePath);
        if (! $image) {
            return [
                'image_path' => '',
                'image_variants' => [],
            ];
        }

        $origW = imagesx($image);
        $origH = imagesy($image);

        foreach (self::WIDTHS as $width) {
            $targetW = min($width, $origW);
            $targetH = max(1, (int) round($origH * ($targetW / $origW)));

            $resized = imagecreatetruecolor($targetW, $targetH);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);

            $webpRel = "menu/{$slug}-{$width}.webp";
            imagewebp($resized, $disk->path($webpRel), 82);
            $variants['webp'][(string) $width] = $webpRel;

            if ($hasAvif) {
                $avifRel = "menu/{$slug}-{$width}.avif";
                @imageavif($resized, $disk->path($avifRel), 62);
                if (file_exists($disk->path($avifRel))) {
                    $variants['avif'][(string) $width] = $avifRel;
                } else {
                    unset($variants['avif'][(string) $width]);
                }
            }

            imagedestroy($resized);
        }

        imagedestroy($image);

        return [
            'image_path' => $variants['webp']['800'],
            'image_variants' => $variants,
        ];
    }

    private static function loadOrientedImage(string $path): ?\GdImage
    {
        $info = @getimagesize($path);
        if (! $info) {
            return null;
        }

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
            default => null,
        };

        if (! $image) {
            return null;
        }

        if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($path);
            $orientation = $exif['Orientation'] ?? 1;
            $rotated = match ($orientation) {
                3 => imagerotate($image, 180, 0),
                6 => imagerotate($image, -90, 0),
                8 => imagerotate($image, 90, 0),
                default => null,
            };
            if ($rotated instanceof \GdImage) {
                imagedestroy($image);
                $image = $rotated;
            }
        }

        return $image;
    }
}
