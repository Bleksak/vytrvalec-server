<?php

namespace App\Requests;

use DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;

class SeasonCreateRequest extends BaseRequest
{
    #[NotNull(message: 'blank')]
    protected ?DateTime $start = null;
    #[NotNull(message: 'blank')]
    protected ?DateTime $end = null;

    #[NotBlank(message: 'blank')]
    protected ?string $charityName = null;

    #[NotBlank(message: 'blank')]
    protected ?string $charityDescription = null;

    public function getCharityName(): ?string
    {
        return $this->charityName;
    }

    public function getCharityDescription(): ?string
    {
        return $this->charityDescription;
    }

    public function getStart(): ?DateTime
    {
        return $this->start;
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    protected function validateBeginDate(): ?ConstraintViolationInterface
    {
        $today = new DateTime();
        if ($today > $this->getStart()) {
            return new ConstraintViolation('invalid_date', 'invalid_date', [], null, 'start', $this->getStart());
        }

        return null;
    }

    protected function validateEndDate(): ?ConstraintViolationInterface
    {
        if($this->getStart() > $this->getEnd()) {
            return new ConstraintViolation('before_start', 'before_start', [], null, 'end', $this->getEnd());
        }

        return null;
    }

}