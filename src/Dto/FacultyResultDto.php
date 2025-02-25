<?php

namespace App\Dto;

class FacultyResultDto
{
    public function __construct(
        public readonly int $faculty,
        public readonly int $distance,
    ) {
    }
}
