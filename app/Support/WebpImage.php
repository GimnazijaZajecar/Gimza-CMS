<?php

namespace App\Support;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Претвара отпремљене слике (JPG, PNG, GIF, WebP) у WebP формат.
 * Слике шире од MAX_WIDTH се смањују да би се сајт брже учитавао.
 */
class WebpImage
{
    public const MAX_WIDTH = 1920;

    public const QUALITY = 82;

    /**
     * Чува слику као .webp на јавном диску и враћа путању (нпр. galerija/01J....webp).
     */
    public static function store(UploadedFile $file, string $directory, string $disk = 'public', int $maxWidth = self::MAX_WIDTH): string
    {
        $webp = static::convert($file->getRealPath(), $maxWidth);

        $path = trim($directory, '/').'/'.Str::lower((string) Str::ulid()).'.webp';

        Storage::disk($disk)->put($path, $webp, 'public');

        return $path;
    }

    /**
     * Враћа садржај WebP слике.
     */
    public static function convert(string $sourcePath, int $maxWidth = self::MAX_WIDTH, int $quality = self::QUALITY): string
    {
        if (! function_exists('imagewebp')) {
            throw new RuntimeException('PHP екстензија GD нема подршку за WebP. Укључите GD са WebP подршком.');
        }

        $contents = @file_get_contents($sourcePath);
        $image = $contents === false ? false : @imagecreatefromstring($contents);

        if (! $image instanceof GdImage) {
            throw new RuntimeException('Фајл није исправна слика.');
        }

        $image = static::fixOrientation($image, $sourcePath);

        if (! imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);

        if (imagesx($image) > $maxWidth) {
            $resized = imagescale($image, $maxWidth, -1, IMG_BICUBIC);

            if ($resized instanceof GdImage) {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $image = $resized;
            }
        }

        ob_start();
        imagewebp($image, null, $quality);

        return (string) ob_get_clean();
    }

    /**
     * Слике са телефона често су „положене“; EXIF ознака каже како да се окрену.
     */
    protected static function fixOrientation(GdImage $image, string $path): GdImage
    {
        if (! function_exists('exif_read_data') || @exif_imagetype($path) !== IMAGETYPE_JPEG) {
            return $image;
        }

        $orientation = (int) ((@exif_read_data($path) ?: [])['Orientation'] ?? 1);

        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };

        return $rotated instanceof GdImage ? $rotated : $image;
    }
}
