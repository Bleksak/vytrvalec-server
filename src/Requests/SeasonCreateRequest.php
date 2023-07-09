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
    protected ?DateTime $beginDate = null;
    #[NotNull(message: 'blank')]
    protected ?DateTime $endDate = null;

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

    public function getBeginDate(): ?DateTime
    {
        return $this->beginDate;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    protected function validateBeginDate(): ?ConstraintViolationInterface
    {
        $today = new DateTime();
        if ($today > $this->getBeginDate()) {
            return new ConstraintViolation('invalid_date', 'invalid_date', [], null, 'beginDate', $this->getBeginDate());
        }

        return null;
    }

    protected function validateEndDate(): ?ConstraintViolationInterface
    {
        if($this->getBeginDate() > $this->getEndDate()) {
            return new ConstraintViolation('before_beginDate', 'before_beginDate', [], null, 'endDate', $this->getEndDate());
        }

        return null;
    }

}