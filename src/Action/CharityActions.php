<?php

namespace App\Action;

use App\Dto\CharityDto;
use App\Entity\Charity;
use App\Repository\CharityRepository;

final class CharityActions
{
    public function __construct(private readonly CharityRepository $charityRepository)
    {
    }

    public function create(CharityDto $dto): int
    {
        $charity = new Charity($dto->name, $dto->description);
        $this->charityRepository->save($charity, true);

        return $charity->getId();
    }

    public function update(Charity $charity, CharityDto $dto): void
    {
        $charity->setName($dto->name ?? $charity->getName());
        $charity->setDescription($dto->description ?? $charity->getDescription());

        $this->charityRepository->save($charity, true);
    }

    public function remove(Charity $charity): bool
    {
        $seasons = $this->charityRepository->findSeasonsByCharity($charity);

        if (!empty($seasons)) {
            return false;
        }

        $this->charityRepository->remove($charity, true);

        return true;
    }
}
