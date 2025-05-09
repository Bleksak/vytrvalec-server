<?php

declare(strict_types=1);

namespace App\Dto\Faculty\Response;

use OpenApi\Attributes as OA;

final readonly class FacultyResponseDto
{
    public function __construct(
        #[OA\Property]
        public int $id,
        #[OA\Property]
        public string $name,
        #[OA\Property]
        public string $shortcut,
        #[OA\Property]
        public bool $visible,
        #[OA\Property]
        public ?int $parentId,
    ) {
    }
}
