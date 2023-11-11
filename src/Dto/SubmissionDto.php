<?php

namespace App\Dto;

use App\Entity\Activity;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SubmissionDto
{
    public ?int $elevation = null;
    public ?int $distance = null;
    public ?UploadedFile $image = null;
    public ?Activity $activity = null;
    public ?DateTimeImmutable $updatedAt = null;
}
