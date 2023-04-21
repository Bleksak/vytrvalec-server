<?php

namespace App\Requests;

use App\Entity\Charity;
use DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;

class SeasonCreateRequest extends BaseRequest 
{
  #[NotNull()]
  protected ?DateTime $beginDate = null;
  
  #[NotBlank()]
  protected ?string $charityName = null;
  
  #[NotBlank()]
  protected ?string $charityDescription = null;
  
  protected function autoValidateRequest(): bool
  {
	return true;
  }
  
  protected function isApi(): bool
  {
	return true;
  }
  
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
  
  protected function validateBeginDate(): ?ConstraintViolationInterface
  {
    $today = new DateTime();
    if($today < $this->getBeginDate()) {
      return new ConstraintViolation('bad_begin_date', 'bad_begin_date', [], null, 'beginDate', $this->getBeginDate());
    }
    
    return null;
  }
  
}