<?php

declare(strict_types=1);

namespace App\Action;

use App\Dto\Faculty\FacultyCreateDto;
use App\Dto\Faculty\FacultyUpdateDto;
use App\Entity\Faculty;
use App\Repository\FacultyRepository;
use App\Utils\AbstractProperty;

final readonly class FacultyActions
{
    public function __construct(
        private FacultyRepository $facultyRepository,
    ) {
    }

    public function create(FacultyCreateDto $dto): int
    {
        $faculty = new Faculty($dto->name, $dto->shortcut, $dto->visible);
        $this->facultyRepository->save($faculty, true);

        return $faculty->getId();
    }

    /**
     * @return array<string>
     */
    public function update(Faculty $faculty, FacultyUpdateDto $dto): array
    {
        $faculty->setName($dto->name ?? $faculty->getName());
        $faculty->setShortcut($dto->shortcut ?? $faculty->getShortcut());
        $faculty->setVisible($dto->visible ?? $faculty->isVisible());

        if (AbstractProperty::isInitialized($dto, 'parent')) {
            if ($dto->parent === $faculty->getId()) {
                return ['parent' => 'invalid_value'];
            }

            $parent = null;

            if ($dto->parent !== null) {
                $parent = $this->facultyRepository->find($dto->parent);
                if ($parent === null || $parent->getParent() !== null) {
                    return ['parent' => 'invalid_value'];
                }
            }

            $faculty->setParent($parent);
        }

        $this->facultyRepository->save($faculty, true);

        return [];
    }
}
