<?php

namespace App\Dto;

class SubmissionStateDto
{
    public ?\DateTimeImmutable $updatedAt = null;
    public ?bool $state = null;
    public string $message;
}
