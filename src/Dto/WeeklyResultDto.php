<?php

namespace App\Dto;

final class WeeklyResultDto
{
    /**
     * @param array<int,ActivityResultDto> $activities
     */
    public function __construct(
        public readonly int $week,
        public array $activities,
    ) {
    }
}
