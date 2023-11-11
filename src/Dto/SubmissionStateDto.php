<?php

namespace App\Dto;

use DateTime;

class SubmissionStateDto
{
    public ?DateTime $updatedAt = null;
    public ?bool $state = null;
    public string $message;
}
