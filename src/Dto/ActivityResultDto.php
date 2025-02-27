<?php

namespace App\Dto;

final class ActivityResultDto
{
    /**
     * @var ExtraPointsDto[]
     **/
    public array $extras = [];

    /**
     * @param array<int, FacultyResultDto> $results
     **/
    public function __construct(
        public readonly int $activity,
        public readonly array $results,
    ) {
    }
}
