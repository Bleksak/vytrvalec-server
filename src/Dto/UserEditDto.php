<?php

namespace App\Dto;

use App\Entity\Faculty;

class UserEditDto
{
    public string $email;
    public string $firstName;
    public string $lastName;
    public Faculty $faculty;
    public bool $banned;
    public array $roles;
}
