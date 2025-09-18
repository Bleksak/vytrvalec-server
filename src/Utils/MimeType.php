<?php

declare(strict_types=1);

namespace App\Utils;

enum MimeType: string
{
    case JPEG = 'image/jpeg';
    case PNG = 'image/png';
    case BMP = 'image/bmp';
    case WEBP = 'image/webp';
    case GIF = 'image/gif';
    case AVIF = 'image/avif';
    case HEIF = 'image/heif';
    case HEIC = 'image/heic';
    case SVG = 'image/svg+xml';

    /**
     * @return array<MimeType>
     */
    public static function default(MimeType ...$additionalTypes): array
    {
        return [
            self::JPEG,
            self::PNG,
            self::BMP,
            self::WEBP,
            self::GIF,
            self::AVIF,
            self::HEIF,
            self::HEIC,
            ...$additionalTypes,
        ];
    }

    /**
     * @return array<MimeType>
     */
    public static function all(): array
    {
        return self::default(self::SVG);
    }
}
