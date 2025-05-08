<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;

final class TotalStatisticsDto
{
    public function __construct(
        #[OA\Property(example: 534)]
        public int $users,

        /**
         * @var array<ActivityStatisticsDto>
         */
        #[OA\Property]
        public array $activities,
    ) {
    }
}
