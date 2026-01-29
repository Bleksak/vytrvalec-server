<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Image;

final readonly class ImagePath
{
    public function __construct(
        private string $applicationPath,
    ) {}

    public function fullPath(string|Image $image): string
    {
        if (\is_string($image)) {
            return $this->applicationPath . $image;
        }

        return $this->applicationPath . $image->getPath();
    }
}
