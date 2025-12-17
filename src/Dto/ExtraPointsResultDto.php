<?php

declare(strict_types=1);

namespace App\Dto;

final class ExtraPointsResultDto
{
    public function __construct(
        public int $user,
        public int $activityId,
        public int $facultyId,
        public int $value,
    ) {}
}
