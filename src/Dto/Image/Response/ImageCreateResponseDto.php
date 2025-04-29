<?php

declare(strict_types=1);

namespace App\Dto\Image\Response;

use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;

final class ImageCreateResponseDto
{
    public function __construct(
        #[OA\Property]
        public Uuid $uuid,
        #[OA\Property]
        public string $path,
        #[OA\Property]
        public \DateTimeImmutable $uploadedAt,
        #[OA\Property]
        public ?\DateTimeImmutable $usedAt,
    ) {
    }
}
