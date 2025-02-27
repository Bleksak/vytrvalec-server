<?php

namespace App\Dto;

use OpenApi\Attributes as OA;

final class ActivityStatisticsDto
{
    public function __construct(
        #[OA\Property(example: 'Běh a chůze')]
        public string $activity,

        #[OA\Property(example: '900')]
        public int $distance,
    ) {
    }
}
