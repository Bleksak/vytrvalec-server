<?php

declare(strict_types=1);

namespace App\Dto\Image;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use OpenApi\Attributes as OA;

final class ImageUploadDto
{
    #[OA\Property(type: 'file')]
    public UploadedFile $image;
}
