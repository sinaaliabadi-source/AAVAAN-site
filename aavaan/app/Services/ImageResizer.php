<?php

namespace App\Services;

use RuntimeException;

class ImageResizer
{
    private const MAX_SIDE  = 2000;
    private const MAX_BYTES = 1_048_576; // 1 MB

    public function resizeAndSave(string $sourcePath, string $destPath): void
    {
        $info = @getimagesize($sourcePath);
        if (!$info) {
            throw new RuntimeException("Cannot read image: {$sourcePath}");
        }

        [, , $type] = $info;

        $src = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG  => $this->pngOnWhite($sourcePath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp')
                ? imagecreatefromwebp($sourcePath)
                : throw new RuntimeException('WebP support not available'),
            default => throw new RuntimeException("Unsupported image type: {$type}"),
        };

        if (!$src) {
            throw new RuntimeException("Failed to create GD resource from: {$sourcePath}");
        }

        // Fix EXIF orientation for JPEG
        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($sourcePath);
            $src  = $this->fixOrientation($src, (int) ($exif['Orientation'] ?? 1));
        }

        $width  = imagesx($src);
        $height = imagesy($src);

        // Resize if the longest side exceeds MAX_SIDE
        $maxSide = max($width, $height);
        if ($maxSide > self::MAX_SIDE) {
            $ratio = self::MAX_SIDE / $maxSide;
            $newW  = (int) round($width  * $ratio);
            $newH  = (int) round($height * $ratio);
            $dst   = imagecreatetruecolor($newW, $newH);
            imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
            imagedestroy($src);
            $src = $dst;
        }

        // Save as JPEG, stepping quality down until the file fits within MAX_BYTES
        for ($quality = 85; $quality >= 30; $quality -= 10) {
            imagejpeg($src, $destPath, $quality);
            if (filesize($destPath) <= self::MAX_BYTES) {
                break;
            }
        }

        imagedestroy($src);
    }

    /**
     * Load a PNG and composite it onto a white background (flattens transparency).
     */
    private function pngOnWhite(string $path): \GdImage|false
    {
        $src = imagecreatefrompng($path);
        if (!$src) return false;

        $w   = imagesx($src);
        $h   = imagesy($src);
        $dst = imagecreatetruecolor($w, $h);
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
        imagealphablending($src, true);
        imagecopy($dst, $src, 0, 0, 0, 0, $w, $h);
        imagedestroy($src);

        return $dst;
    }

    private function fixOrientation(\GdImage $image, int $orientation): \GdImage
    {
        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => null,
        };

        if ($rotated) {
            imagedestroy($image);
            return $rotated;
        }

        return $image;
    }
}
