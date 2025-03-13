<?php

namespace App\Messages;

use App\Entity\Season;

final class SeasonStartMessage
{
    public function __construct(
        public Season $season,
    ) {
    }
}
