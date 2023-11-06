<?php

namespace App\Dto;

use App\Entity\Activity;

class ActivityResultDto
{
    public Activity $activity;

    /**
    * @var FacultyResultDto[]
    **/
    public array $results = [];

    /**
    * @var ExtraPointsDto[]
    **/
    public array $extras = [];

    /**
    * @param array<int, FacultyResultDto> $results
    * @param array<int, ExtraPointsDto> $extras
    **/
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }
}
