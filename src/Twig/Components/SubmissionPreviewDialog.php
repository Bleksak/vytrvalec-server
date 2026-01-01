<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Submission;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class SubmissionPreviewDialog
{
    use DefaultActionTrait;

    /**
     * @var array<int, Submission>
     **/
    #[LiveProp]
    public array $submissions = [];

    #[LiveProp]
    public ?Submission $currentSubmission = null;

    public function __construct() {}

    /**
     * @param array<int, Submission> $submissions
     */
    public function mount(array $submissions): void
    {
        $this->submissions = $submissions;
    }

    #[LiveAction]
    public function setSubmission(
        #[LiveArg('submission_id')] int $submissionId,
    ): void {
        $this->currentSubmission = $this->submissions[$submissionId] ?? null;
    }
}
