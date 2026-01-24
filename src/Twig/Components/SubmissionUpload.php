<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Action\SubmissionActions;
use App\Dto\Submission\SubmissionServerCreateDto;
use App\Entity\Activity;
use App\Entity\User;
use App\Form\SubmissionCreateFormType;
use App\Repository\ActivityRepository;
use App\Repository\SeasonRepository;
use App\Utils\MimeType;
use App\Utils\Toast\ToastContext;
use App\Utils\Toast\ToastManager;
use App\Utils\Toast\ToastType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
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
final class SubmissionUpload extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public SubmissionServerCreateDto $initialData;

    /** @var array<int, Activity> */
    private array $activities;

    /** @var list<string> */
    #[LiveProp]
    public array $allowedMimeTypes = [];

    public function __construct(
        private SubmissionActions $submissionActions,
        private ActivityRepository $activityRepository,
        private SeasonRepository $seasonRepository,
        private ToastManager $toastManager,
        LocaleSwitcher $localeSwitcher,
    ) {
        $this->activities = $this->activityRepository->findAllWithTranslations($localeSwitcher->getLocale());

        $this->allowedMimeTypes = \array_map(
            static fn(MimeType $mimeType): string => $mimeType->value,
            MimeType::default(),
        );
    }

    public function mount(): void
    {
        $this->initialData = new SubmissionServerCreateDto();
    }

    #[\Override]
    public function instantiateForm(): FormInterface
    {
        return $this->createForm(
            SubmissionCreateFormType::class,
            $this->initialData,
            ['activities' => $this->activities],
        );
    }

    #[LiveAction]
    public function submit(#[CurrentUser] User $user): void
    {
        $season = $this->seasonRepository->findCurrentSeason();

        if ($season === null) {
            $this->toastManager->add(
                ToastType::Error,
                ToastContext::SubmissionCreate,
                message: 'submission.create.no_season',
            );

            return;
        }

        $this->submitForm();

        /** @var SubmissionServerCreateDto */
        $data = $this->getForm()->getData();

        if ($data->distance !== null) {
            $data->distance *= 1000;
        }

        $submission = $this->submissionActions->createServer(
            $data,
            $user,
            $season,
        );

        if ($submission === null) {
            $this->toastManager->add(
                ToastType::Error,
                ToastContext::SubmissionCreate,
                message: 'submission.create.error',
            );
            return;
        }

        $this->initialData = new SubmissionServerCreateDto();
        $this->resetForm();

        $this->toastManager->add(
            ToastType::Success,
            ToastContext::SubmissionCreate,
            message: 'submission.create.success',
        );

        $this->emit('submission-create', [
            'submission_id' => $submission->id,
            'season_id' => $season->id,
        ]);
    }

    #[LiveListener('image-upload')]
    public function imageUploaded(#[LiveArg('uuid')] string $uuid): void
    {
        $this->formValues['image_uuid'] = $uuid;
    }
}
