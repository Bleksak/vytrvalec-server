<?php

namespace App\Dto;

use App\Entity\Faculty;

class FacultyResultDto
{
    public Faculty $faculty;
    public int $distance;

    public function __construct(Faculty $faculty, int $distance)
    {
        $this->faculty = $faculty;
        $this->distance = $distance;
    }
}

