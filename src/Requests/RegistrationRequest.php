<?php

namespace App\Requests;

use App\Attributes\DB;
use App\Entity\Faculty;

class RegistrationRequest extends BaseRequest
{

    protected ?string $email;
    protected ?string $password;
    protected ?string $firstName;
    protected ?string $lastName;

    #[DB]
    protected ?Faculty $faculty;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getFaculty(): ?Faculty
    {
        return $this->faculty;
    }

    protected function autoValidateRequest(): bool
    {
        return false;
    }
}