<?php

namespace App\Dto;

use DateTimeImmutable;

class SubmissionStateDto
{
    public ?DateTimeImmutable $updatedAt = null;
    public ?bool $state = null;
    public string $message;
}
