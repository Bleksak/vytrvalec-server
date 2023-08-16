<?php

namespace App\Requests;

use App\Attributes\DB;
use App\Entity\Activity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class SubmissionRequest extends BaseRequest
{
    #[NotBlank(message: 'blank')]
    #[Type('integer', message: 'non_integer')]
    protected ?int $distance;

    #[Type('integer', message: 'non_integer')]
    protected ?int $elevation = 0;

    #[NotBlank(message: 'blank')]
    #[Image(mimeTypesMessage: 'bad_image')]
    protected ?UploadedFile $image;

    #[DB]
    protected ?Activity $activity;

    public function getDistance(): ?int {
        return $this->distance;
    }

    public function getElevation(): ?int {
        return $this->elevation;
    }

    public function getImage(): ?UploadedFile {
        return $this->image;
    }

    public function getActivity(): ?Activity {
        return $this->activity;
    }

    protected function validateFile()
    {
    }
}