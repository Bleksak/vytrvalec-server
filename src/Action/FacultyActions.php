<?php

namespace App\Action;

use App\Dto\FacultyDto;
use App\Entity\Faculty;
use App\Repository\FacultyRepository;
use App\Utils\Property;

final class FacultyActions
{
    public function __construct(
        private readonly FacultyRepository $facultyRepository,
    ) {
    }

    public function create(FacultyDto $dto): int
    {
        $faculty = new Faculty($dto->name, $dto->shortcut, $dto->visible);
        $this->facultyRepository->save($faculty, true);

        return $faculty->getId();
    }

    /**
     * @return array<string>
     */
    public function update(Faculty $faculty, FacultyDto $dto): array
    {
        $faculty->setName($dto->name ?? $faculty->getName());
        $faculty->setShortcut($dto->shortcut ?? $faculty->getShortcut());
        $faculty->setVisible($dto->visible ?? $faculty->isVisible());

        if (Property::isInitialized($dto, 'parent')) {
            if ($dto->parent === null) {
                $faculty->setParent(null);
            } else {
                $parent = $this->facultyRepository->find($dto->parent);
                if ($parent === null || $parent->getParent() !== null) {
                    return ['parent' => 'invalid_value'];
                }

                $faculty->setParent($parent);
            }
        }

        $this->facultyRepository->save($faculty, true);

        return [];
    }
}
