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
use App\Notifications\Firebase\Firebase;
use App\Notifications\VytrvalecNotification;
use App\Repository\ActivityRepository;
use App\Repository\ImageRepository;
use App\Repository\ProfileCacheRepository;
use App\Repository\SubmissionRepository;
use App\Services\VytrvalecMailer;

final readonly class SubmissionActions
{
    public function __construct(
        private SubmissionRepository $submissionRepository,
        private ProfileCacheRepository $profileCacheRepository,
        private VytrvalecMailer $mailer,
        private ImageRepository $imageRepository,
        private ActivityRepository $activityRepository,
    ) {
    }

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

        if ($image === null || $image->getUsedAt() !== null) {
            return ['image' => 'invalid'];
        }

        $submission = new Submission(
            $user,
            $activity,
            $season,
            $image,
            $dto->distance,
            $dto->elevation ?? 0
        );

        $this->submissionRepository->save($submission, true);

        return [];
    }

    /**
     * @return array<int,string>
     */
    public function setState(Submission $submission, SubmissionStateDto $dto): array
    {
        if ($dto->updatedAt !== $submission->getUpdatedAt()) {
            return ['mismatch_updated_at'];
        }

        $submission->setMessage($dto->message);

        if ($dto->state) {
            // noop when already accepted, otherwise profile cache would stack
            if (!$submission->isReviewed() || !$submission->isAccepted()) {
                $this->profileCacheRepository->addCache($submission);
            }
        } else {
            if ($submission->isReviewed() && $submission->isAccepted()) {
                $this->profileCacheRepository->removeCache($submission);
            }

            // if ($submission->getUser()->getToken() !== null) {
            //     $this->firebase->send(new VytrvalecNotification($submission->getUser(), $dto->message));
            // }

            $now = new \DateTimeImmutable();

            if ($submission->getDate()->diff($now)->m < 2) {
                $this->mailer->send($submission->getUser(), new SubmissionRejectedEmailTemplate($submission, $dto->message));
            }
        }

        $submission->setReviewed(true);
        $submission->setAccepted($dto->state);

        $this->submissionRepository->save($submission, true);

        return [];
    }

    public function delete(Submission $submission): void
    {
        $this->submissionRepository->remove($submission, true);
    }

    /**
     * @return array<string,string>
     */
    public function update(Submission $submission, SubmissionEditDto $dto): array
    {
        if ($submission->getUpdatedAt() !== $dto->updatedAt) {
            return ['updated_at' => 'mismatch'];
        }

        if ($dto->imageUuid !== null) {
            $image = $this->imageRepository->find($dto->imageUuid);

            if ($image === null || $image->getUsedAt() !== null) {
                return ['image' => 'invalid'];
            }

            $submission->setImage($image);
        }

        if ($dto->distance !== null) {
            $submission->setDistance($dto->distance);
        }

        if ($dto->elevation !== null) {
            $submission->setElevation($dto->elevation);
        }

        if ($dto->activityId !== null) {
            $activity = $this->activityRepository->find($dto->activityId);

            if ($activity === null) {
                return ['activity_id' => 'invalid'];
            }

            $submission->setActivity($activity);
        }

        $submission->setMessage('');
        $submission->setReviewed(false);

        $this->submissionRepository->save($submission, true);

        return [];
    }
}
