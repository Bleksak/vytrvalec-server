<?php

namespace App\Requests;

use App\Entity\Activity;
use App\Requests\DB;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;

class SubmissionRequest extends BaseRequest {
  #[NotNull()]
  #[DB]
  protected ?Activity $activity = null;
  
  #[Type('integer')]
  #[NotBlank()]
  #[NotNull()]
  protected int $distance;
  
  #[Type('integer')]
  #[NotNull()]
  protected int $elevation = 0;
  
  #[Image()]
  #[NotBlank()]
  #[NotNull()]
  protected ?UploadedFile $screenshot;
  
  public function getActivity(): ?Activity
  {
    return $this->activity;
  }
  
  public function getDistance(): int
  {
    return $this->distance;
  }
  
  public function getElevation(): int
  {
    return $this->elevation;
  }
  
  public function getScreenshot(): ?UploadedFile
  {
    return $this->screenshot;
  }
  
  protected function isApi(): bool
  {
	return true;
  }
}