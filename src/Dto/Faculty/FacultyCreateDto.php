<?php

declare(strict_types=1);

namespace App\Dto\Faculty;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final class FacultyCreateDto
{
    #[OA\Property]
    #[Assert\NotBlank(allowNull: false)]
    public FacultyCreateTranslationDto $translations;

    #[Assert\NotBlank(allowNull: false)]
    #[OA\Property(example: 'FAV')]
    public string $shortcut;

    #[Assert\NotNull]
    #[OA\Property(example: true)]
    public bool $visible;

    #[Assert\Type(type: 'integer', message: 'invalid_value')]
    #[OA\Property(type: 'integer')]
    public ?int $parent;

    #[Assert\Type(type: 'string', message: 'invalid_value')]
    #[Assert\CssColor(formats: [
        Assert\CssColor::HEX_LONG_WITH_ALPHA,
        Assert\CssColor::HEX_LONG,
        Assert\CssColor::HEX_SHORT_WITH_ALPHA,
        Assert\CssColor::HEX_SHORT,
    ], message: 'invalid')]
    #[OA\Property(type: 'string')]
    public string $color;
}
