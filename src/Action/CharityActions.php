<?php

declare(strict_types=1);

namespace App\Action;

use App\Dto\Charity\CharityCreateDto;
use App\Dto\Charity\CharityUpdateDto;
use App\Entity\Charity;
use App\Entity\CharityTranslation;
use App\Repository\CharityRepository;
use App\Repository\ImageRepository;

final readonly class CharityActions
{
    public function __construct(
        private CharityRepository $charityRepository,
        private ImageRepository $imageRepository,
    ) {
    }

    /**
     * @return Charity|array<string, string>
     */
    public function create(CharityCreateDto $dto): Charity|array
    {
        $image = null;

        if ($dto->imageUuid !== null) {
            $image = $this->imageRepository->find($dto->imageUuid);

            if ($image === null || $image->getUsedAt() !== null) {
                return ['image' => 'invalid'];
            }

            $image->setUsedAt(new \DateTime());
        }

        $charity = new Charity(
            $dto->translations,
            $image,
            $dto->website
        );

        $this->charityRepository->save($charity, true);

        return $charity;
    }

    public function update(Charity $charity, CharityUpdateDto $dto): void
    {
        $nameTranslations = $dto->translations?->name?->toArray() ?? [];
        $descriptionTranslations = $dto->translations?->description?->toArray() ?? [];

        foreach ($nameTranslations as $locale => $translation) {
            assert($translation !== null, 'Translation cannot be null!');

            $charityTranslation = $charity->translations->get($locale);

            if ($charityTranslation === null) {
                $charityTranslation = new CharityTranslation(
                    $charity,
                    $locale,
                    $translation,
                    $descriptionTranslations[$locale] ?? ''
                );
                $charity->addTranslation($charityTranslation);
            }

            $charityTranslation->name = $translation;
        }

        foreach ($descriptionTranslations as $locale => $translation) {
            assert($translation !== null, 'Translation cannot be null!');

            $charityTranslation = $charity->translations->get($locale);

            if ($charityTranslation === null) {
                $charityTranslation = new CharityTranslation(
                    $charity,
                    $locale,
                    $nameTranslations[$locale] ?? '',
                    $translation,
                );
                $charity->addTranslation($charityTranslation);
            }

            $charityTranslation->description = $translation;
        }

        if ($dto->image !== null) {
            $image = $this->imageRepository->find($dto->image);

            if ($image !== null && $image->getUsedAt() === null) {
                $oldImage = $charity->getImage();
                $charity->setImage($image);
                $image->setUsedAt(new \DateTime());
                $oldImage?->setUsedAt(null);
            }
        }

        $charity->setWebsite($dto->website ?? $charity->getWebsite());

        $this->charityRepository->save($charity, true);
    }

    public function remove(Charity $charity): bool
    {
        $seasons = $this->charityRepository->findSeasonsByCharity($charity);

        if (count($seasons) !== 0) {
            return false;
        }

        $this->charityRepository->remove($charity, true);

        return true;
    }
}
