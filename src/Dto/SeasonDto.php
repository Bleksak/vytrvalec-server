<?php

namespace App\Dto;

use App\Entity\Charity;

final class SeasonDto
{
    public ?\DateTime $start;
    public ?\DateTime $end;
    public ?Charity $charity;
}
