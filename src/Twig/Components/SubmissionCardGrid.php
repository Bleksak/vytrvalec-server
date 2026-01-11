<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Submission;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class SubmissionCardGrid
{
    use DefaultActionTrait;

    /** @var array<int, Submission> */
    #[LiveProp(writable: false)]
    public array $submissions;

    public function __construct() {}

    /**
     * @param array<int, Submission> $submissions
     */
    public function mount(array $submissions): void
    {
        $this->submissions = $submissions;
    }

    #[LiveListener('submission-delete')]
    public function deleteListener(
        #[LiveArg('submission_id')] int $submissionId,
    ): void {
        unset($this->submissions[$submissionId]);
    }
}
