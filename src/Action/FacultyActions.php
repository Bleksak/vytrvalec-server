<?php

namespace App\Action;

use App\Dto\FacultyDto;
use App\Entity\Faculty;
use App\Repository\FacultyRepository;

class FacultyActions
{
    public function __construct(
        private readonly FacultyRepository $facultyRepository,
    )
    {
        
    }

    public function create(FacultyDto $dto): void
    {
        $faculty = new Faculty($dto->name, $dto->shortcut, $dto->visible);

        $this->facultyRepository->save($faculty, true);
    }

    public function update(Faculty $faculty, FacultyDto $dto): void
    {
        $faculty->setName($dto->name ?? $faculty->getName());
        $faculty->setShortcut($dto->shortcut ?? $faculty->getShortcut());
        $faculty->setVisible($dto->visible ?? $faculty->isVisible());

        $this->facultyRepository->save($faculty, true);
    }
}
