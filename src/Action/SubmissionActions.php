<?php

declare(strict_types=1);

namespace App\Action;

use App\Dto\Submission\SubmissionCreateDto;
use App\Dto\Submission\SubmissionEditDto;
use App\Dto\Submission\SubmissionStateDto;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use App\Notifications\EmailTemplate\SubmissionRejectedEmailTemplate;
use App\Repository\ActivityRepository;
use App\Repository\ImageRepository;
use App\Repository\ProfileCacheRepository;
use App\Repository\SubmissionRepository;
use App\Services\VytrvalecMailer;
use DateTime;

final readonly class SubmissionActions
{
    public function __construct(
        private SubmissionRepository $submissionRepository,
        private ProfileCacheRepository $profileCacheRepository,
        private VytrvalecMailer $mailer,
        private ImageRepository $imageRepository,
        private ActivityRepository $activityRepository,
    ) {}

    /**
     * @return array<string, string>
     */
    public function create(
        SubmissionCreateDto $dto,
        User $user,
        Season $season,
    ): array {
        $activity = $this->activityRepository->find($dto->activityId);

        if ($activity === null) {
            return ['activity_id' => 'invalid'];
        }

        $image = $this->imageRepository->find($dto->imageUuid);

        if ($image === null || $image->usedAt !== null) {
            return ['image' => 'invalid'];
        }

        $image->usedAt = new \DateTime();

        $submission = new Submission(
            $user,
            $activity,
            $season,
            $image,
            $dto->distance,
            new DateTime(),
            // $dto->date,
            $dto->elevation ?? 0,
        );

        $this->submissionRepository->save($submission, true);

        return [];
    }

    /**
     * @return array<int,string>
     */
    public function setState(
        User $issuer,
        Submission $submission,
        SubmissionStateDto $dto,
    ): array {
        if (
            $dto->updatedAt?->getTimestamp() !== $submission->updatedAt->getTimestamp()
        ) {
            return ['mismatch_updated_at'];
        }

        // Submission is reviewed and DTO has the same state as the submission => noop
        // TODO(@bleksak): The message might have changed, but we will handle that another day
        if (
            $submission->reviewed === true
            && $submission->accepted === $dto->state
        ) {
            return [];
        }

        $submission->message = $dto->message;

        $this->handleCacheUpdate($submission, $dto);

        // TODO(@bleksak): tady se da frajerovi zaspamovat email kdyby admin furt schvaloval a zamital aktivitu :D
        if (!$dto->state) {
            // if ($submission->getUser()->getToken() !== null) {
            //     $this->firebase->send(new VytrvalecNotification($submission->getUser(), $dto->message));
            // }

            $now = new \DateTimeImmutable();

            if ($submission->date->diff($now)->m < 2) {
                $template = new SubmissionRejectedEmailTemplate(
                    $submission,
                    $dto->message,
                );
                $template->replyTo = $issuer->email;

                $this->mailer->send($submission->user, $template);
            }
        }

        $submission->reviewed = true;
        $submission->accepted = $dto->state;

        $this->submissionRepository->save($submission, true);

        return [];
    }

    public function delete(Submission $submission): void
    {
        if ($submission->image !== null) {
            $submission->image->usedAt = null;
            $this->imageRepository->save($submission->image);
        }

        $this->submissionRepository->remove($submission, true);
    }

    /**
     * @return array<string,string>
     */
    public function update(
        Submission $submission,
        SubmissionEditDto $dto,
    ): array {
        if (
            $submission->updatedAt->getTimestamp() !== $dto->updatedAt?->getTimestamp()
        ) {
            return ['updated_at' => 'mismatch'];
        }

        if ($dto->imageUuid !== null) {
            $image = $this->imageRepository->find($dto->imageUuid);

            if (
                $image === null
                || $image->usedAt !== null && $submission->image !== $image
            ) {
                return ['image' => 'invalid'];
            }

            $oldImage = $submission->image;
            $image->usedAt = new \DateTime();
            $submission->image = $image;

            if ($oldImage !== null) {
                $oldImage->usedAt = null;
            }
        }

        if ($dto->distance !== null) {
            $submission->distance = $dto->distance;
        }

        if ($dto->elevation !== null) {
            $submission->elevation = $dto->elevation;
        }

        if ($dto->activityId !== null) {
            $activity = $this->activityRepository->find($dto->activityId);

            if ($activity === null) {
                return ['activity_id' => 'invalid'];
            }

            $submission->activity = $activity;
        }

        $submission->message = '';
        $submission->reviewed = false;

        $this->submissionRepository->save($submission, true);

        return [];
    }

    private function handleCacheUpdate(
        Submission $submission,
        SubmissionStateDto $dto,
    ): void {
        if ($dto->state && !$submission->reviewed && !$submission->accepted) {
            // noop when already accepted, otherwise profile cache would stack
            $this->profileCacheRepository->addCache($submission);
        }

        if (!$dto->state && $submission->accepted) {
            $this->profileCacheRepository->removeCache($submission);
        }
    }
}
