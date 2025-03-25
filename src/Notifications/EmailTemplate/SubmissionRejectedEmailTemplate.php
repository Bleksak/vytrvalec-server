<?php

namespace App\Notifications\EmailTemplate;

use App\Entity\Submission;
use App\Notifications\EmailTemplate;

final class SubmissionRejectedEmailTemplate extends EmailTemplate
{
    public function __construct(Submission $submission, string $message)
    {
        $this->mergeContext([
            'submission' => $submission,
            'message' => $message,
        ]);
    }

    public function getSubject(): string
    {
        return 'Měsíční Vytrvalec - Zamítnutí příspěvku';
    }

    public function getTemplate(): string
    {
        return 'emails/submission_rejected.twig';
    }
}
