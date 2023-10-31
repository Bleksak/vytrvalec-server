<?php

namespace App\Dto;

use App\Entity\Activity;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SubmissionDto
{
    public ?int $elevation = 0;
    public ?int $distance;
    public ?UploadedFile $image = null;
    public ?Activity $activity;
}
