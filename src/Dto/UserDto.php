<?php

namespace App\Dto;

use App\Entity\Faculty;

class UserDto
{
    public string $email;
    public string $username;
    public string $password;
    public string $firstName;
    public string $lastName;
    public Faculty $faculty;
}
