<?php

namespace App\Requests;

class LoginRequest extends BaseRequest
{
    protected ?string $username = null;
    protected ?string $password = null;

    protected function isApi(): bool
    {
        return true;
    }

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}