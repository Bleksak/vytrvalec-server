<?php

namespace App\Dto;

final class SubmissionStateDto
{
    public ?\DateTimeImmutable $updatedAt = null;
    public ?bool $state = null;
    public string $message;
}
