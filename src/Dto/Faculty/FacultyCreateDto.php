<?php

declare(strict_types=1);

namespace App\Dto\Faculty;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final class FacultyCreateDto
{
    #[OA\Property(example: 'Fakulta aplikovaných věd')]
    #[Assert\NotBlank(allowNull: false)]
    public string $name;

    #[Assert\NotBlank(allowNull: false)]
    #[OA\Property(example: 'FAV')]
    public string $shortcut;

    #[Assert\NotNull]
    #[OA\Property(example: true)]
    public bool $visible;

    #[Assert\Type(type: 'integer', message: 'invalid_value')]
    #[OA\Property(type: 'integer')]
    public ?int $parent;
}
