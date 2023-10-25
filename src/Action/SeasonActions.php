<?php

namespace App\Action;

use App\Entity\Season;
use App\Repository\SeasonRepository;

class SeasonActions
{
    public function __construct(
        private SeasonRepository $seasonRepository,
    )
    {
    }

    public function create(Season $season): void
    {
        // here we assume the season entity is complete!
        $this->seasonRepository->save($season, true);
    }

}
