<?php

declare(strict_types=1);

namespace App\Dto\SeasonResult;

use App\Dto\ExtraPointsDto;

final readonly class SeasonResultRankDto
{
    /**
     * @param list<SeasonResultRankRowDto> $rows
     * @param list<ExtraPointsDto> $extras
     */
    public function __construct(
        public int $totalDistance,
        public int $totalPoints,
        public array $rows,
        public array $extras,
    ) {}
}
