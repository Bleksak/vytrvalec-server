<?php

namespace App\Action;

use App\CustomLogic\SeasonResult;
use App\Entity\Cache;
use App\Entity\Season;
use App\Repository\CacheRepository;

final class CacheActions
{
    public function __construct(
        private readonly CacheRepository $cacheRepository,
        private readonly SeasonResult $seasonResult,
    ) {
    }

    public function cacheSeason(Season $season): void
    {
        $cache = $this->cacheRepository->findOneBy(['season' => $season->getId()]);
        $result = $this->seasonResult->calculate($season);

        if (empty($result)) {
            return;
        }

        if ($cache !== null) {
            $cache->setData($result);
            $this->cacheRepository->save($cache, true);
        } else {
            $this->cacheRepository->save(new Cache($season, $result), true);
        }
    }
}
