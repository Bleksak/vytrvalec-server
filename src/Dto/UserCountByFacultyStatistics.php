<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;

final class UserCountByFacultyStatistics
{
    public function __construct(
        #[OA\Property]
        public int $faculty,
        #[OA\Property(example: 70)]
        public int $count,
    ) {}
}
