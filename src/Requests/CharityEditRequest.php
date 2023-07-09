<?php

namespace App\Requests;

use App\Attributes\DB;
use App\Entity\Charity;

class CharityEditRequest extends BaseRequest 
{
  #[DB]
  protected ?Charity $charity = null;
  protected ?string $charityName = null;
  protected ?string $charityDescription = null;

  protected function autoValidateRequest(): bool
  {
	return true;
  }
  
  public function getCharity(): ?Charity 
  {
    return $this->charity;
  }
  
  public function getCharityName(): ?string 
  {
    return $this->charityName;
  }
  
  public function getCharityDescription(): ?string 
  {
    return $this->charityDescription;
  }
}