<?php

namespace App\Requests;

use App\Entity\Submission;

class SubmissionStateRequest extends BaseRequest
{
    #[DB]
    protected ?Submission $submission;

    public function getSubmission(): ?Submission
    {
        return $this->submission;
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
