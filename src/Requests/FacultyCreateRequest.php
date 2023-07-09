<?php

namespace App\Requests;

use App\Entity\Faculty;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;

#[UniqueEntity(fields: ['name', 'shortcut'], message: 'not_unique', entityClass: Faculty::class)]
class FacultyCreateRequest extends BaseRequest
{
    #[NotBlank]
    protected ?string $name = null;

    #[NotBlank]
    protected ?string $shortcut = null;

    #[NotBlank]
    protected ?bool $visible = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getShortcut(): ?string
    {
        return $this->shortcut;
    }

    public function getVisible(): ?bool
    {
       return $this->visible;
    }
}