<?php

namespace App\Dto;

final class WeeklySubmissionSum
{
    public function __construct(
        public int $distance,
        public int $faculty,
        public int $activity,
    ) {
    }
}
