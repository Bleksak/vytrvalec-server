<?php

namespace App\Action;

use App\Dto\ActivityDto;
use App\Entity\Activity;
use App\Repository\ActivityRepository;

class ActivityActions
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
    )
    {
    }

    public function create(ActivityDto $dto): void
    {
        $activity = new Activity($dto->name, $dto->minElevation);

        $this->activityRepository->save($activity, true);
    }

    public function update(Activity $activity, ActivityDto $dto): void
    {
        $activity->setName($dto->name ?? $activity->getName());
        $activity->setMinElevation($dto->minElevation ?? $activity->getMinElevation());

        $this->activityRepository->save($activity, true);
    }

    public function delete(Activity $activity): bool
    {
        if($this->activityRepository->submissionsCount($activity) === 0) {
            return false;
        }

        $this->activityRepository->remove($activity, true);
        return true;
    }
}
