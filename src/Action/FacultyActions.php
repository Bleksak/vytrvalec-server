<?php

declare(strict_types=1);

namespace App\Action;

use App\Dto\Faculty\FacultyCreateDto;
use App\Dto\Faculty\FacultyUpdateDto;
use App\Entity\Faculty;
use App\Entity\FacultyTranslation;
use App\Repository\FacultyRepository;
use App\Utils\AbstractProperty;

final readonly class FacultyActions
{
    public function __construct(
        private FacultyRepository $facultyRepository,
    ) {}

    public function create(FacultyCreateDto $dto): int
    {
        $faculty = new Faculty(
            $dto->translations,
            $dto->shortcut,
            $dto->visible,
            $dto->color,
        );

        $this->facultyRepository->save($faculty, true);

        return $faculty->id;
    }

    /**
     * @return array<string>
     */
    public function update(Faculty $faculty, FacultyUpdateDto $dto): array
    {
        $nameTranslations = $dto->translations?->name?->toArray() ?? [];

        foreach ($nameTranslations as $locale => $translation) {
            \assert($translation !== null, 'Translation cannot be null!');

            $facultyTranslation = $faculty->translations->get($locale);

            if ($facultyTranslation === null) {
                $facultyTranslation = new FacultyTranslation(
                    $faculty,
                    $locale,
                    $translation,
                );
                $faculty->addTranslation($facultyTranslation);
            }

            $facultyTranslation->name = $translation;
        }

        $faculty->shortcut = $dto->shortcut ?? $faculty->shortcut;
        $faculty->visible = $dto->visible ?? $faculty->visible;
        $faculty->color = $dto->color ?? $faculty->color;

        if (AbstractProperty::isInitialized($dto, 'parent')) {
            if ($dto->parent === $faculty->id) {
                return ['parent' => 'invalid_value'];
            }

            $parent = null;

            if ($dto->parent !== null) {
                $parent = $this->facultyRepository->find($dto->parent);
                if ($parent === null || $parent->parent !== null) {
                    return ['parent' => 'invalid_value'];
                }
            }

            $faculty->parent = $parent;
        }

        $this->facultyRepository->save($faculty, true);

        return [];
    }
}
