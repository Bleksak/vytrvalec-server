<?php

declare(strict_types=1);

namespace App\Notifications\EmailTemplate;

use App\Entity\Submission;
use App\Notifications\AbstractEmailTemplate;

final class SubmissionRejectedEmailTemplate extends AbstractEmailTemplate
{
    public function __construct(Submission $submission, string $message)
    {
        $this->mergeContext([
            'submission' => $submission,
            'message' => $message,
        ]);
    }

    #[\Override]
    public function getSubject(): string
    {
        return 'Měsíční Vytrvalec - Zamítnutí příspěvku';
    }

    #[\Override]
    public function getTemplate(): string
    {
        return 'emails/submission_rejected.twig';
    }
}
