<?php

namespace App\Dto;

final class PasswordResetDto
{
    public string $password;
    public string $passwordResetToken;
}
