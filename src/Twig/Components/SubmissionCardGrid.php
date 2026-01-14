<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Submission;
use App\Repository\SubmissionRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class SubmissionCardGrid
{
    use DefaultActionTrait;

    /** @var non-empty-array<int, Submission> */
    #[LiveProp(writable: true)]
    public array $submissions;

    #[LiveProp]
    public int $seasonId;

    public function __construct(
        private SubmissionRepository $submissionRepository,
    ) {}

    /**
     * @param array<int, Submission> $submissions
     */
    public function mount(int $seasonId, array $submissions): void
    {
        $this->seasonId = $seasonId;
        $this->submissions = $submissions;
    }

    #[LiveListener('submission-update')]
    public function updateListener(
        #[LiveArg('submission_id')] int $submissionId,
    ): void {
        if (!isset($this->submissions[$submissionId])) {
            return;
        }

        $this->submissions[$submissionId] =
            $this->submissionRepository->find($submissionId);

        krsort($this->submissions);
    }

    #[LiveListener('submission-delete')]
    public function deleteListener(
        #[LiveArg('submission_id')] int $submissionId,
    ): void {
        unset($this->submissions[$submissionId]);
    }
}
