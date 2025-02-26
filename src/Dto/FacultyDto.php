<?php

namespace App\Dto;

use OpenApi\Attributes as OA;

final class FacultyDto
{
    #[OA\Property(example: 'Fakulta aplikovaných věd')]
    public ?string $name;

    #[OA\Property(example: 'FAV')]
    public ?string $shortcut;

    #[OA\Property(example: true)]
    public ?bool $visible;
}
