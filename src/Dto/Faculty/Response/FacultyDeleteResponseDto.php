<?php

declare(strict_types=1);

namespace App\Dto\Faculty\Response;

use OpenApi\Attributes as OA;

final readonly class FacultyDeleteResponseDto
{
    /**
     * @param list<string> $faculty
     */
    public function __construct(
        #[OA\Property(example: ['has_users'], type: 'array', items: new OA\Items(type: 'string'))]
        public array $faculty,
    ) {}
}
