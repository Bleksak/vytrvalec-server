<?php

namespace App\Action;

use App\Dto\SubmissionDto;
use App\Dto\SubmissionStateDto;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use App\Notifications\EmailTemplate\SubmissionRejectedEmailTemplate;
use App\Notifications\Firebase\Firebase;
use App\Notifications\VytrvalecEmail;
use App\Notifications\VytrvalecNotification;
use App\Repository\ProfileCacheRepository;
use App\Repository\SubmissionRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\MailerInterface;

final class SubmissionActions
{
    public function __construct(
        private readonly Firebase $firebase,
        private readonly SubmissionRepository $submissionRepository,
        private readonly ProfileCacheRepository $profileCacheRepository,
        private readonly ParameterBagInterface $parameterBag,
        private readonly Filesystem $fs,
        private readonly MailerInterface $mailer,
    ) {
    }

    private function uploadImage(UploadedFile $image): ?string
    {
        $dirname = $this->parameterBag->get('kernel.project_dir').'/public';

        do {
            $uniquePath = '/uploads/'.uniqid(more_entropy: true).'.jpg';
            $absolutePath = $dirname.$uniquePath;
        } while ($this->fs->exists($absolutePath));

        $tmpPath = $uniquePath.'.tmp';

        $newFile = $image->move($dirname, $tmpPath);

        try {
            $img = new \Imagick($newFile->getRealPath());
            $profiles = $img->getImageProfiles('icc');

            $img->stripImage();
            if (!empty($profiles)) {
                $img->profileImage('icc', $profiles['icc']);
            }

            $img->setImageFormat('jpeg');
            $img->setImageCompressionQuality(90);
            $img->writeImage($absolutePath);
        } catch (\ImagickException) {
            return null;
        } finally {
            $this->fs->remove($newFile);
        }

        return $uniquePath;
    }

    /**
     * @return array<string>
     */
    public function create(
        SubmissionDto $dto,
        User $user,
        Season $season,
    ): array {
        $imagePath = $this->uploadImage($dto->image);
        if ($imagePath === null) {
            return ['image_error'];
        }

        $submission = new Submission($user, $dto->activity, $season, $imagePath, $dto->distance, $dto->elevation ?? 0);

        $this->submissionRepository->save($submission, true);

        return [];
    }

    /**
     * @return array<int,string>
     */
    public function setState(Submission $submission, SubmissionStateDto $dto): array
    {
        if ($dto->updatedAt != $submission->getUpdatedAt()) {
            return ['mismatch_updated_at'];
        }

        $submission->setMessage($dto->message);

        if ($dto->state) {
            // noop when already accepted, otherwise profile cache would stack
            if (!$submission->isReviewed() || !$submission->isAccepted()) {
                $this->profileCacheRepository->addCache($submission, true);
            }
        } else {
            if ($submission->isReviewed() && $submission->isAccepted()) {
                $this->profileCacheRepository->removeCache($submission, true);
            }

            if ($submission->getUser()->getToken() !== null) {
                $this->firebase->send(new VytrvalecNotification($submission->getUser(), $dto->message));
            }

            $emailAddress = $submission->getUser()->getEmail();
            $now = new \DateTimeImmutable();

            if ($emailAddress !== null && $submission->getDate()->diff($now)->m < 2) {
                $this->mailer->send(new VytrvalecEmail($emailAddress, new SubmissionRejectedEmailTemplate($submission, $dto->message)));
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
     * @return array<int,string>
     */
    public function update(Submission $submission, SubmissionDto $dto): array
    {
        if ($submission->getUpdatedAt() != $dto->updatedAt) {
            return ['mismatch_updated_at'];
        }

        if ($dto->image !== null) {
            $imagePath = $this->uploadImage($dto->image);
            if ($imagePath === null) {
                return ['image_error'];
            }

            $submission->setImage($imagePath);
        }

        if ($dto->distance !== null) {
            $submission->setDistance($dto->distance);
        }

        if ($dto->elevation !== null) {
            $submission->setElevation($dto->elevation);
        }

        if ($dto->activity !== null) {
            $submission->setActivity($dto->activity);
        }

        $submission->setReviewed(false);

        $this->submissionRepository->save($submission, true);

        return [];
    }
}
