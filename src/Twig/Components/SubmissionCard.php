<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Submission;
use App\Repository\SubmissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class SubmissionCard extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public ?Submission $submission;

    public function __construct(
        private SubmissionRepository $submissionRepository,
    ) {}

    public function mount(?Submission $submission = null): void
    {
        $this->submission = $submission;
    }

    #[LiveAction]
    public function delete(): void
    {
        // NOTE: This is a workaround for LiveComponent calling everything twice
        if ($this->submission === null) {
            return;
        }

        $this->emitUp('submission-delete', [
            'submission_id' => $this->submission->id,
        ]);

        $this->submissionRepository->remove($this->submission, true);
    }
}
