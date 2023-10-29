<?php

namespace App\Action;

use App\Dto\CharityDto;
use App\Entity\Charity;
use App\Repository\CharityRepository;

class CharityActions
{
    public function __construct(private readonly CharityRepository $charityRepository)
    {
    }

    public function create(CharityDto $dto): void
    {
        $charity = new Charity($dto->name, $dto->description);
        $this->charityRepository->save($charity, true);
    }

    public function update(Charity $charity, CharityDto $dto): void
    {
        $charity->setName($dto->name ?? $charity->getName());
        $charity->setDescription($dto->description ?? $charity->getDescription());

        $this->charityRepository->save($charity, true);
    }
}
