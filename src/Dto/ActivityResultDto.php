<?php

namespace App\Dto;

class ActivityResultDto
{
    /**
    * @var ExtraPointsDto[]
    **/
    public array $extras = [];

    /**
    * @param array<int, FacultyResultDto> $results
    * @param array<int, ExtraPointsDto> $extras
    **/
    public function __construct(
        public readonly int $activity,
        public readonly array $results,
    )
    {
    }
}
