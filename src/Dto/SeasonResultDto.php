<?php

namespace App\Dto;

final class SeasonResultDto
{
    /**
     * @param array<WeeklyResultDto> $results
     * @param array<OutlierActivity> $outliers
     */
    public function __construct(
        public array $results,
        public array $outliers,
    ) {
    }
}
