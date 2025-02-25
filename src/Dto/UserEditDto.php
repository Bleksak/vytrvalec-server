<?php

namespace App\Dto;

use App\Entity\Faculty;

class UserEditDto
{
    public ?string $email = null;
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?Faculty $faculty = null;
    public ?bool $banned = null;

    /**
     * @var array<string>
     */
    public ?array $roles = null;
}
