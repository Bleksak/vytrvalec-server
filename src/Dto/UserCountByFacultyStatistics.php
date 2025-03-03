<?php

namespace App\Dto;

// use App\Entity\Faculty;
use OpenApi\Attributes as OA;

final class UserCountByFacultyStatistics
{
    public function __construct(
        #[OA\Property]
        public int $faculty,
        #[OA\Property(example: 70)]
        public int $count,
    ) {
    }
}
