<?php

declare(strict_types=1);

namespace App\Dto\Faculty;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class FacultyMappingDto
{
    public function __construct(
        #[OA\Property(type: 'integer')]
        #[Assert\NotBlank(message: 'blank', allowNull: false)]
        public int $faculty,

        #[OA\Property(type: 'integer')]
        #[Assert\NotBlank(message: 'blank', allowNull: true)]
        public ?int $parent,
    ) {}
}
