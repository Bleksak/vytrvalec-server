<?php

namespace App\Dto;

use App\Entity\Charity;
use DateTime;

class SeasonDto
{
    public ?DateTime $start;
    public ?DateTime $end;
    public ?Charity $charity;
}
