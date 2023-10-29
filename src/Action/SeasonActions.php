<?php

namespace App\Action;

use App\Dto\SeasonDto;
use App\Entity\Season;
use App\Repository\SeasonRepository;

class SeasonActions
{
    public function __construct(
        private SeasonRepository $seasonRepository,
    )
    {
    }

    public function create(SeasonDto $seasonDto): void
    {
        $season = new Season($seasonDto->start, $seasonDto->end, seasonDto->charity);

        $this->seasonRepository->save($season, true);
    }

}
