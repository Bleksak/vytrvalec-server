<?php

declare(strict_types=1);

namespace App\Dto;

use App\Dto\Activity\Response\ActivityResponseDto;
use OpenApi\Attributes as OA;

final class ActivityStatisticsDto
{
    public function __construct(
        #[OA\Property(example: 'Běh a chůze')]
        public ActivityResponseDto $activity,
        #[OA\Property(example: 900)]
        public int $distance,
    ) {}
}
