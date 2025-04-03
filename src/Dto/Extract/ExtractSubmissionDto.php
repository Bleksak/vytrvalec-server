<?php

namespace App\Dto\Extract;

final class ExtractSubmissionDto
{
    public function __construct(
        public int $activityId,
        public int $seasonId,
        public bool $accepted,
        public int $distance,
        public int $elevation,
        public string $image,
    ) {
    }
}
