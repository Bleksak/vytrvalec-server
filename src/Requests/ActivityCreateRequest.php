<?php

namespace App\Requests;

use App\Entity\Activity;
use App\Validation\Constraint\UniqueValue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;

#[UniqueValue(fields: ['name'], em: Activity::class)]
class ActivityCreateRequest extends BaseRequest
{
    #[NotBlank(message: 'blank')]
    protected ?string $name = null;

    #[NotBlank(message: 'blank')]
    protected ?int $minElevation = 0;

    protected ?bool $active = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMinElevation(): ?int
    {
        return $this->minElevation;
    }

    protected function validateMinElevation(): ?ConstraintViolationInterface
    {
        if($this->getMinElevation() < 0) {
            return new ConstraintViolation('negative', null, [], null, 'min_elevation', $this->getMinElevation());
        }

        return null;
    }
}
