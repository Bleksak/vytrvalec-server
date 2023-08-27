<?php

namespace App\Requests;

use App\Entity\Faculty;
use App\Validation\Constraint\UniqueValue;
use Symfony\Component\Validator\Constraints\NotBlank;

#[UniqueValue(fields: ['name'], em: Faculty::class)]
#[UniqueValue(fields: ['shortcut'], em: Faculty::class)]
class FacultyUpdateRequest extends BaseRequest
{
    #[NotBlank(message: 'blank', allowNull: true)]
    protected ?string $name = null;

    #[NotBlank(message: 'blank', allowNull: true)]
    protected ?string $shortcut = null;

    #[NotBlank(message: 'blank', allowNull: true)]
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
