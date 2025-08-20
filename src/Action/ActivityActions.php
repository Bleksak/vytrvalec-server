<?php

declare(strict_types=1);

namespace App\Action;

use App\Dto\Activity\ActivityCreateDto;
use App\Dto\Activity\ActivityUpdateDto;
use App\Entity\Activity;
use App\Entity\ActivityTranslation;
use App\Repository\ActivityRepository;
use App\Repository\ImageRepository;

final readonly class ActivityActions
{
    public function __construct(
        private ActivityRepository $activityRepository,
        private ImageRepository $imageRepository,
    ) {
    }

    public function create(ActivityCreateDto $dto): ?Activity
    {
        $icon = $this->imageRepository->find($dto->icon);

        if ($icon === null) {
            return null;
        }

        $activity = new Activity($dto->translations, $dto->minElevation, $icon);

        $this->activityRepository->save($activity, true);

        return $activity;
    }

    public function update(Activity $activity, ActivityUpdateDto $dto): void
    {
        $nameTranslations = $dto->translations?->name?->toArray() ?? [];

        foreach ($nameTranslations as $locale => $translation) {
            \assert($translation !== null, 'Translation cannot be null!');

            $activityTranslation = $activity->getTranslations()->get($locale);

            if ($activityTranslation === null) {
                $activityTranslation = new ActivityTranslation($activity, $locale, $translation);
                $activity->addTranslation($activityTranslation);
            }

            $activityTranslation->name = $translation;
        }

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
