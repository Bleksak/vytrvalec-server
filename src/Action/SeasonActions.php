<?php

namespace App\Action;

use App\Dto\SeasonDto;
use App\Entity\Season;
use App\Repository\SeasonRepository;

final class SeasonActions
{
    public function __construct(
        private readonly SeasonRepository $seasonRepository,
    ) {
    }

    public function create(SeasonDto $seasonDto): int
    {
        $existingSeason = $this->seasonRepository->findByStartMonth($seasonDto->start);

        if ($existingSeason !== null) {
            return -1;
        }

        $season = new Season($seasonDto->start, $seasonDto->end, $seasonDto->charity);

        $this->seasonRepository->save($season, true);

        return $season->getId();
    }
}
