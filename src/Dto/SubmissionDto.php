<?php

namespace App\Dto;

use App\Entity\Activity;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class SubmissionDto
{
    public ?int $elevation = null;
    public ?int $distance = null;
    public ?UploadedFile $image = null;
    public ?Activity $activity = null;
    public ?\DateTimeImmutable $updatedAt = null;
}
