<?php

namespace App\Dto;

use App\Entity\Charity;
use DateTimeImmutable;

class SeasonDto
{
    public DateTimeImmutable $start;
    public DateTimeImmutable $end;
    public Charity $charity;
}
