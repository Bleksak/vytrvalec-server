<?php

declare(strict_types=1);

namespace App\Action;

use App\CustomLogic\SeasonResultCalculator;
use App\Entity\Cache;
use App\Entity\Season;
use App\Repository\SeasonCacheRepository;

final readonly class SeasonCacheActions
{
    public function __construct(
        private SeasonCacheRepository $cacheRepository,
        private SeasonResultCalculator $seasonResult,
    ) {}

    public function cacheSeason(Season $season): void
    {
        $now = new \DateTime();

        if ($season->getEnd() > $now) {
            return;
        }

        $cache = $this->cacheRepository->findBySeason($season);
        $result = $this->seasonResult->calculate($season);

        if ($cache !== null) {
            $cache->setData($result);
            $this->cacheRepository->save($cache, true);
        } else {
            $this->cacheRepository->save(new Cache($season, $result), true);
        }
    }

    public function isCached(Season $season): bool
    {
        return $this->cacheRepository->isCached($season);
    }
}
