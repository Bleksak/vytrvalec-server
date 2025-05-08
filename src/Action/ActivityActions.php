<?php

declare(strict_types=1);

namespace App\Action;

use App\Dto\Activity\ActivityCreateDto;
use App\Dto\Activity\ActivityUpdateDto;
use App\Entity\Activity;
use App\Repository\ActivityRepository;

final class ActivityActions
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
    ) {
    }

    public function create(ActivityCreateDto $dto): int
    {
        $activity = new Activity($dto->name, $dto->minElevation);
        $this->activityRepository->save($activity, true);

        return $activity->getId();
    }

    public function update(Activity $activity, ActivityUpdateDto $dto): void
    {
        $activity->setName($dto->name ?? $activity->getName());
        $activity->setMinElevation($dto->minElevation ?? $activity->getMinElevation());

        $this->activityRepository->save($activity, true);
    }

    public function delete(Activity $activity): bool
    {
        if ($this->activityRepository->submissionsCount($activity) === 0) {
            return false;
        }

        $this->activityRepository->remove($activity, true);

        return true;
    }
}
