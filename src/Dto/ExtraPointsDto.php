<?php

namespace App\Dto;

class ExtraPointsDto
{
    public function __construct(
        public readonly int $user, 
        public readonly string $name, 
        public readonly int $value, 
        public readonly int $points
    )
    {
    }
}
