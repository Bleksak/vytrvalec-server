<?php

declare(strict_types=1);

namespace App\Dto;

final class SeasonResultDto
{
    /**
     * @param list<WeeklyResultDto> $results
     * @param list<OutlierActivity> $outliers
     */
    public function __construct(
        public array $results,
        public array $outliers,
    ) {}
}
