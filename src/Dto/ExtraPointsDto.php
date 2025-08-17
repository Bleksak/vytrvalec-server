<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;

final readonly class ExtraPointsDto
{
    public function __construct(
        #[OA\Property]
        public AnonymizedUser $user,
        #[OA\Property(example: 1)]
        public int $faculty,
        #[OA\Property(example: 'daily_distance')]
        public string $name,
        #[OA\Property(example: 2700)]
        public int $value,
        #[OA\Property(example: 1)]
        public int $points,
    ) {}
}
