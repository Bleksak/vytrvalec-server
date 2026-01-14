<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Action\SubmissionActions;
use App\Dto\Submission\SubmissionServerEditDto;
use App\Entity\Activity;
use App\Entity\Submission;
use App\Form\SubmissionEditFormType;
use App\Repository\ActivityRepository;
use App\Repository\SubmissionRepository;
use App\Utils\MimeType;
use App\Utils\Toast\ToastContext;
use App\Utils\Toast\ToastManager;
use App\Utils\Toast\ToastType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\LocaleSwitcher;
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
    #[LiveProp(writable: true)]
    public array $submissions = [];

    /** @var array<int, Activity> **/
    #[LiveProp]
    public array $activities;

    #[LiveProp]
    public ?Submission $currentSubmission = null;

    /** @var list<string> */
    #[LiveProp]
    public array $allowedMimeTypes = [];

    public function __construct(
        private SubmissionRepository $submissionRepository,
        private ActivityRepository $activityRepository,
        private SubmissionActions $submissionActions,
        private ToastManager $toastManager,
        LocaleSwitcher $localeSwitcher,
    ) {
        $this->activities = $this->activityRepository->findAllWithTranslations($localeSwitcher->getLocale());
        $this->allowedMimeTypes = \array_map(
            static fn(MimeType $mimeType): string => $mimeType->value,
            MimeType::default(),
        );
    }

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

    #[LiveAction]
    public function setSubmission(
        #[LiveArg('submission_id')] int $submissionId,
    ): void {
        $submission = $this->submissionRepository->find($submissionId);

        if ($submission === null) {
            return;
        }

        $this->formValues = [
            'distance' => $submission->distance / 1000,
            'elevation' => $submission->elevation,
            'image_uuid' => $submission->image?->uuid->toString(),
            'activity' => $submission->activity->id,
            'updated_at' => $submission->updatedAt->format('Y-m-d H:i:s'),
        ];

        $this->emit('updateImage', [
            'image' => $submission->image?->getPath(),
        ]);

        $this->currentSubmission = $submission;
    }

    #[LiveAction]
    public function submit(): void
    {
        if ($this->currentSubmission === null) {
            return;
        }

        $this->submitForm();

        /** @var SubmissionServerEditDto */
        $data = $this->getForm()->getData();

        if ($data->distance !== null) {
            $data->distance *= 1000;
        }

        $this->submissionActions->update($this->currentSubmission, $data);

        $this->toastManager->add(
            ToastType::Success,
            ToastContext::SubmissionEdit,
            message: 'submission.edit.success',
        );

        $this->emit('submission-update', [
            'submission_id' => $this->currentSubmission->id,
        ]);

        $this->liveResponder->dispatchBrowserEvent('dialog:close');
    }

    #[LiveListener('image-upload')]
    public function imageUploaded(#[LiveArg('uuid')] string $uuid): void
    {
        $this->formValues['image_uuid'] = $uuid;
    }
}
