<?php

namespace App\Requests;

use App\Attributes\DB;
use App\Entity\Faculty;
use App\Entity\User;
use App\Validation\Constraint\UniqueValue;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\PasswordStrength;

#[UniqueValue(fields: ['email'], em: User::class, message: 'not_unique_email')]
class UserCreateRequest extends BaseRequest
{
    #[Email(message: 'invalid_format')]
    #[NotBlank(message: 'blank')]
    protected ?string $email;

//    #[PasswordStrength(minScore: 2, message: 'weak_password')]
    #[NotBlank(message: 'blank')]
    protected ?string $password;

    #[NotBlank(message: 'blank')]
    protected ?string $firstName;

    #[NotBlank(message: 'blank')]
    protected ?string $lastName;

    #[NotNull(message: 'invalid')]
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
}