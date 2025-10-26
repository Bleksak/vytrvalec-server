<?php

declare(strict_types=1);

namespace App\Dto\User;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final class UserEditDto
{
    #[OA\Property]
    #[Assert\NotBlank]
    public ?string $email = null;

    #[OA\Property]
    #[Assert\NotBlank]
    public ?string $firstName = null;

    #[OA\Property]
    #[Assert\NotBlank]
    public ?string $lastName = null;

    #[OA\Property]
    #[Assert\NotBlank]
    public ?int $facultyId = null;

    #[OA\Property]
    #[Assert\NotBlank]
    public ?bool $banned = null;

    /**
     * @var array<string>|null
     */
    #[OA\Property]
    #[Assert\NotBlank]
    public ?array $roles = null;
}
