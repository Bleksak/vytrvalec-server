<?php

namespace App\Dto;

use App\Entity\Faculty;
use OpenApi\Attributes as OA;

final class UserDto
{
    #[OA\Property]
    public string $email;

    #[OA\Property]
    public string $username;

    #[OA\Property]
    public string $password;

    #[OA\Property]
    public string $firstName;

    #[OA\Property]
    public string $lastName;

    #[OA\Property]
    public Faculty $faculty;

    #[OA\Property]
    public bool $gdpr;
}
