<?php

namespace App\Requests;

use App\Entity\Faculty;

class RegistrationRequest extends BaseRequest
{

    protected ?string $username;
    protected ?string $password;
    protected ?string $first_name;
    protected ?string $last_name;

    #[DB]
    protected ?Faculty $faculty;

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function getFaculty(): ?Faculty
    {
        return $this->faculty;
    }

    protected function isApi(): bool
    {
        return true;
    }

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}