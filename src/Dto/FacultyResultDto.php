<?php

namespace App\Dto;

final class FacultyResultDto
{
    public function __construct(
        public readonly int $faculty,
        public readonly int $distance,
    ) {
    }
}
