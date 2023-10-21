<?php

namespace App\Requests;

use App\Entity\Activity;
use App\Validation\Constraint\UniqueValue;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;

#[UniqueValue(fields: ['name'], em: Activity::class)]
class ActivityUpdateRequest extends BaseRequest
{
    protected ?string $name = null;
    protected ?int $minElevation = null;
    protected ?bool $active = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMinElevation(): ?int
    {
        return $this->minElevation;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    protected function validateMinElevation(): ?ConstraintViolationInterface
    {
        if($this->getMinElevation() !== null && $this->getMinElevation() < 0) {
            return new ConstraintViolation('negative', null, [], null, 'minElevation', $this->getMinElevation());
        }

        return null;
    }
}
