<?php

declare(strict_types=1);

namespace App\Action;

use App\Dto\Activity\ActivityCreateDto;
use App\Dto\Activity\ActivityUpdateDto;
use App\Entity\Activity;
use App\Entity\ActivityTranslation;
use App\Repository\ActivityRepository;
use App\Repository\ImageRepository;
use App\Utils\MimeType;

final readonly class ActivityActions
{
    public function __construct(
        private ActivityRepository $activityRepository,
        private ImageRepository $imageRepository,
    ) {}

    public function create(ActivityCreateDto $dto): ?Activity
    {
        $icon = $this->imageRepository->find($dto->icon);

        if ($icon === null || $icon->originalMimeType !== MimeType::SVG) {
            // TODO(@bleksak): Tady musi byt upozorneni ze obrazek musi byt svg.
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

            $activityTranslation = $activity->translations->get($locale);

            if ($activityTranslation === null) {
                $activityTranslation = new ActivityTranslation(
                    $activity,
                    $locale,
                    $translation,
                );
                $activity->addTranslation($activityTranslation);
            }

            $activityTranslation->name = $translation;
        }

        if ($dto->icon !== null) {
            $icon = $this->imageRepository->find($dto->icon);

            if ($icon !== null && $icon->originalMimeType === MimeType::SVG) {
                $activity->icon = $icon;
            }
        }

        $activity->minElevation = $dto->minElevation ?? $activity->minElevation;

        $this->activityRepository->save($activity, true);
    }

    public function delete(Activity $activity): bool
    {
        if ($this->activityRepository->submissionsCount($activity) !== 0) {
            return false;
        }

        $this->activityRepository->remove($activity, true);

        return true;
    }
}
