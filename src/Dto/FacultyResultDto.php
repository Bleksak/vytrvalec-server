<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;

final readonly class FacultyResultDto
{
    public function __construct(
        #[OA\Property(example: 1)]
        public int $faculty,
        #[OA\Property(example: 2250)]
        public int $distance,
    ) {}
}
