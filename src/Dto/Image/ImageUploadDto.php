<?php

declare(strict_types=1);

namespace App\Dto\Image;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ImageUploadDto
{
    #[OA\Property(
        type: 'string',
        format: 'binary',
    )]
    public UploadedFile $image;
}
