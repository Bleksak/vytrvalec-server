<?php

namespace App\Requests;

use App\Attributes\DB;
use App\Entity\Submission;

class SubmissionStateRequest extends BaseRequest
{
    #[DB]
    protected ?Submission $submission;

    public function getSubmission(): ?Submission
    {
        return $this->submission;
    }
    protected function autoValidateRequest(): bool
    {
        return true;
    }
}
