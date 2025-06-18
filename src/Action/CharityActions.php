<?php

declare(strict_types=1);

namespace App\Action;

use App\Dto\Charity\CharityCreateDto;
use App\Dto\Charity\CharityEditDto;
use App\Entity\Charity;
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

            if ($image === null) {
                return ['image' => 'invalid'];
            }

            $image->setUsedAt(new \DateTimeImmutable());
        }

        $charity = new Charity($dto->name, $dto->description, $image, $dto->website);
        $this->charityRepository->save($charity, true);

        return $charity;
    }

    public function update(Charity $charity, CharityEditDto $dto): void
    {
        $charity->setName($dto->name ?? $charity->getName());
        $charity->setDescription($dto->description ?? $charity->getDescription());

        if ($dto->imageUuid !== null) {
            $image = $this->imageRepository->find($dto->imageUuid);

            if ($image !== null) {
                $oldImage = $charity->getImage();
                $charity->setImage($image);
                $image->setUsedAt(new \DateTimeImmutable());
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
