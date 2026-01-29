<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Submission;
use App\Repository\SubmissionRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class SubmissionPreviewDialog
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public ?Submission $currentSubmission = null;

    public function __construct(
        private SubmissionRepository $submissionRepository,
    ) {}

    #[LiveAction]
    public function setSubmission(
        #[LiveArg('submission_id')] int $submissionId,
    ): void {
        $submission = $this->submissionRepository->find($submissionId);

        if ($submission === null) {
            return;
        }

        $this->emit('updateImage', [
            'image' => $submission->image?->getPath(),
        ]);

        $this->currentSubmission = $submission;
    }
}
