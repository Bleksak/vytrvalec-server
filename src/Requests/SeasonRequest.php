<?php

namespace App\Requests;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;

class SeasonRequest extends BaseRequest
{
    #[NotBlank(message: 'blank', allowNull: true)]
    protected ?DateTimeImmutable $start = null;

    #[NotBlank(message: 'blank', allowNull: true)]
    protected ?DateTimeImmutable $end = null;

    #[NotBlank(message: 'blank', allowNull: true)]
    protected ?string $charityName = null;

    #[NotBlank(message: 'blank', allowNull: true)]
    protected ?string $charityDescription = null;

    public function getCharityName(): ?string
    {
        return $this->charityName;
    }

    public function getCharityDescription(): ?string
    {
        return $this->charityDescription;
    }

    public function getStart(): ?DateTimeImmutable
    {
        return $this->start;
    }

    public function getEnd(): ?DateTimeImmutable
    {
        return $this->end;
    }

    protected function populateEndDate(): void
    {
        if($this->end === null) {
            $this->end = $this->start->add(new \DateInterval('P4W'));
        }
    }

    protected function validateBeginDate(): ?ConstraintViolationInterface
    {
        $today = new DateTimeImmutable();
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