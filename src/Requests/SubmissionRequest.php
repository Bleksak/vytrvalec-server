<?php

namespace App\Requests;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class SubmissionRequest extends BaseRequest
{
    protected ?int $distance;
    protected ?int $elevation;
    protected ?UploadedFile $image;

    public function getDistance(): ?int {
        return $this->distance;
    }

    public function getElevation(): ?int {
        return $this->elevation;
    }

    public function getImage(): ?UploadedFile {
        return $this->image;
    }

    protected function validateImage(): ConstraintViolationListInterface
    {
        if(str_starts_with($this->image->getMimeType(), "image/")) {
            return new ConstraintViolationList();
        }

        return new ConstraintViolationList([
            new ConstraintViolation('Bad file type', '', [], null, 'image', $this->image->getMimeType())
        ]);
    }

    protected function isApi(): bool
    {
        return true;
    }

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}