<?php

namespace App\Action;

use App\Dto\SubmissionDto;
use App\Entity\RejectedSubmissionMessage;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use App\Notifications\EmailTemplate\SubmissionRejectedEmailTemplate;
use App\Notifications\Firebase\Firebase;
use App\Notifications\VytrvalecEmail;
use App\Notifications\VytrvalecNotification;
use App\Repository\FacultyCacheRepository;
use App\Repository\ProfileCacheRepository;
use App\Repository\RejectedSubmissionMessageRepository;
use App\Repository\SubmissionRepository;
use App\Repository\UserCacheRepository;
use DateTime;
use Imagick;
use ImagickException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\MailerInterface;

class SubmissionActions
{
    public function __construct(
        private readonly Firebase $firebase,
        private readonly MailerInterface $mailer,
        private readonly SubmissionRepository $submissionRepository,
        private readonly RejectedSubmissionMessageRepository $rejectedSubmissionMessageRepository,
        private readonly FacultyCacheRepository $facultyCacheRepository,
        private readonly UserCacheRepository $userCacheRepository,
        private readonly ProfileCacheRepository $profileCacheRepository,
        private readonly ParameterBagInterface $parameterBag,
        private readonly Filesystem $fs,
    ) {
    }

    private function uploadImage(UploadedFile $image): ?string
    {
        $dirname = $this->parameterBag->get('kernel.project_dir') . '/public';

        do {
            $uniquePath = '/uploads/' . uniqid(more_entropy: true) . '.jpg';
            $absolutePath = $dirname . $uniquePath;
        } while ($this->fs->exists($absolutePath));

        $tmpPath = $uniquePath . '.tmp';

        $newFile = $image->move($dirname, $tmpPath);

        try {
            $img = new Imagick($newFile->getRealPath());
            $profiles = $img->getImageProfiles("icc");

            $img->stripImage();
            if (!empty($profiles)) {
                $img->profileImage("icc", $profiles['icc']);
            }

            $img->setImageFormat('jpeg');
            $img->setImageCompressionQuality(90);
            $img->writeImage($absolutePath);
        } catch (ImagickException) {
            return null;
        } finally {
            $this->fs->remove($newFile);
        }

        return $uniquePath;
    }

    /*
    * @return array<int, string>
    */
    public function create(SubmissionDto $dto, User $user, Season $season): array
    {
        $imagePath = $this->uploadImage($dto->image);
        if ($imagePath === null) {
            return ['image_error'];
        }

        $submission = new Submission($user, $dto->activity, $season, $imagePath, $dto->distance, $dto->elevation ?? 0);

        $this->submissionRepository->save($submission, true);

        return [];
    }

    private function setState(Submission $submission, DateTime $lastUpdatedAt, bool $state): bool
    {
        if ($lastUpdatedAt !== $submission->getUpdatedAt()) {
            return false;
        }

        $submission->setReviewed(true);
        $submission->setAccepted($state);
        $this->submissionRepository->save($submission, true);

        return true;
    }
    /**
     * @return array<int,string>
     */
    public function accept(Submission $submission, DateTime $lastUpdatedAt): array
    {
        if (!$this->setState($submission, $lastUpdatedAt, false)) {
            return ['mismatch_updated_at'];
        }
        $this->profileCacheRepository->addCache($submission, true);

        return [];
    }
    /**
     * @return array<int,string>
     */
    public function reject(Submission $submission, DateTime $lastUpdatedAt, string $message): array
    {
        if (!$this->setState($submission, $lastUpdatedAt, false)) {
            return ['mismatch_updated_at'];
        }

        $this->rejectedSubmissionMessageRepository->save(new RejectedSubmissionMessage($submission, $message));

        $this->firebase->send(new VytrvalecNotification($submission->getUser(), $message));
        $this->mailer->send(new VytrvalecEmail($submission->getUser(), new SubmissionRejectedEmailTemplate($submission, $message)));

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
        if ($submission->getUpdatedAt() !== $dto->updatedAt) {
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

        $this->submissionRepository->save($submission, true);

        return [];
    }
}
