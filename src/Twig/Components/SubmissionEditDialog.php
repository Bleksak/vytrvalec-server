<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Dto\Submission\SubmissionServerEditDto;
use App\Entity\Activity;
use App\Entity\Submission;
use App\Form\SubmissionEditFormType;
use App\Utils\MimeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class SubmissionEditDialog extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentWithFormTrait;

    /** @var array<int, Submission> **/
    #[LiveProp]
    public array $submissions = [];

    /** @var array<int, Activity> **/
    #[LiveProp]
    public array $activities;

    #[LiveProp]
    public ?Submission $currentSubmission = null;

    /** @var list<string> */
    #[LiveProp]
    public array $allowedMimeTypes = [];

    public function __construct() {}

    #[\Override]
    public function instantiateForm(): FormInterface
    {
        return $this->createForm(
            SubmissionEditFormType::class,
            new SubmissionServerEditDto(),
            [
                'activities' => $this->activities,
            ],
        );
    }

    /**
     * @param array<int, Submission> $submissions
     * @param array<int, Activity> $activities
     */
    public function mount(array $submissions, array $activities): void
    {
        $this->submissions = $submissions;
        $this->activities = $activities;

        $this->allowedMimeTypes = \array_map(
            static fn(MimeType $mimeType): string => $mimeType->value,
            MimeType::default(),
        );
    }

    #[LiveAction]
    public function setSubmission(
        #[LiveArg('submission_id')] int $submissionId,
    ): void {
        $submission = $this->submissions[$submissionId] ?? null;
        \assert($submission !== null);

        $this->formValues = [
            'distance' => $submission->distance / 1000,
            'elevation' => $submission->elevation,
            'imageUuid' => $submission->image?->uuid,
            'activity' => $submission->activity->id,
            'updatedAt' => $submission->updatedAt,
        ];

        $this->emit('updateImage', [
            'image' => $submission->image?->getPath(),
        ]);

        $this->currentSubmission = $submission;
    }

    #[LiveAction]
    public function submit(): void
    {
        // TODO(@bleksak): actualyl submit the form
        $this->submitForm();
    }

    #[LiveListener('image-upload')]
    public function imageUploaded(#[LiveArg('uuid')] string $uuid): void
    {
        $this->formValues['imageUuid'] = $uuid;
    }
}
