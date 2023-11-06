<?php

namespace App\Dto;

class ExtraPointsDto
{
    public array $users;
    public string $name;
    public int $score;
    public int $points;

    public function __construct(array $users, string $name, int $score, int $points)
    {
        $this->users = $users;
        $this->name = $name;
        $this->score = $score;
        $this->points = $points;
    }
}
