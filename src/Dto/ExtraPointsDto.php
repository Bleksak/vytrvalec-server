<?php

namespace App\Dto;

use OpenApi\Attributes as OA;

final class ExtraPointsDto
{
    public function __construct(
        #[OA\Property]
        public readonly AnonymizedUser $user,
        #[OA\Property(example: 1)]
        public readonly int $faculty,
        #[OA\Property(example: 'daily_distance')]
        public readonly string $name,
        #[OA\Property(example: 2700)]
        public readonly int $value,
        #[OA\Property(example: 1)]
        public readonly int $points,
    ) {
    }
}
