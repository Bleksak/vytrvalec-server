<?php

declare(strict_types=1);

namespace App\Dto\User\Response;

use App\Dto\Faculty\Response\FacultyResponseDto;
use OpenApi\Attributes as OA;

final readonly class UserResponseDto
{
    /**
     * @param array<string> $roles
     */
    public function __construct(
        #[OA\Property]
        public int $id,
        #[OA\Property]
        public ?string $email,
        #[OA\Property]
        public array $roles,
        #[OA\Property]
        public bool $banned,
        #[OA\Property]
        public bool $mailing,
        #[OA\Property]
        public string $firstName,
        #[OA\Property]
        public string $lastName,
        #[OA\Property]
        public FacultyResponseDto $faculty,
        #[OA\Property]
        public ?bool $anonymize,
    ) {}
}
