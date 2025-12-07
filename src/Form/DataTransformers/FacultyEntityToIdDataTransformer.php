<?php

declare(strict_types=1);

namespace App\Form\DataTransformers;

use App\Entity\Faculty;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @implements DataTransformerInterface<int, Faculty>
 */
final readonly class FacultyEntityToIdDataTransformer implements
    DataTransformerInterface
{
    /**
     * @param array<int, Faculty> $faculties
     */
    public function __construct(
        private array $faculties,
    ) {}

    #[\Override]
    public function transform(mixed $value): mixed
    {
        if (!$value) {
            return null;
        }

        return $this->faculties[$value];
    }

    #[\Override]
    public function reverseTransform(mixed $value): mixed
    {
        return $value?->id;
    }
}
