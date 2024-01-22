<?php

namespace App\Dto;

class UserAccountChangeDto
{
    public string $oldPassword;
    public ?string $password = null;
    public ?string $email = null;
}
