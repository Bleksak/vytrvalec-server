<?php

namespace App\Dto;

final class UserAccountChangeDto
{
    public string $oldPassword;
    public ?string $password = null;
    public ?string $email = null;
}
