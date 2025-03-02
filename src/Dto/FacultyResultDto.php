<?php

namespace App\Dto;

use OpenApi\Attributes as OA;

final class FacultyResultDto
{
    public function __construct(
        #[OA\Property(example: 1)]
        public readonly int $faculty,

        #[OA\Property(example: 2250)]
        public readonly int $distance,
    ) {
    }
}
