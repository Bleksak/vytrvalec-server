<?php

namespace App\Action;

use App\CustomLogic\SeasonResult;
use App\Entity\Cache;
use App\Entity\Season;
use App\Repository\CacheRepository;

class CacheActions
{
    public function __construct(
        private readonly CacheRepository $cacheRepository,
        private readonly SeasonResult $seasonResult,
    ) {
    }

    public function cacheSeason(Season $season): void
    {
        $cache = $this->cacheRepository->findOneBy(['season' => $season->getId()]);

        if($cache !== null) {
            $cache->setData($this->seasonResult->calculate($season));
            $this->cacheRepository->save($cache, true);
        } else {
            $this->cacheRepository->save(new Cache($season, $this->seasonResult->calculate($season)), true);
        }
    }
}
