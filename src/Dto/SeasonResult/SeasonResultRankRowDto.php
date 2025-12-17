<?php

declare(strict_types=1);

namespace App\Dto\SeasonResult;

final class SeasonResultRankRowDto
{
    public function __construct(
        public int $faculty,
        public int $distance,
        public int $points,
    ) {}
}
