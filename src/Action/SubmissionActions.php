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
use Imagick;
use ImagickException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\MailerInterface;

class SubmissionActions
{
    public function __construct(
        private Firebase $firebase,
        private MailerInterface $mailer,
        private SubmissionRepository $submissionRepository,
        private RejectedSubmissionMessageRepository $rejectedSubmissionMessageRepository,
        private FacultyCacheRepository $facultyCacheRepository,
        private UserCacheRepository $userCacheRepository,
        private ProfileCacheRepository $profileCacheRepository,
        private readonly ParameterBagInterface $parameterBag,
        private readonly Filesystem $fs,
    )
    {
    }

    /*
    * @return array<int, string>
    */
    public function create(SubmissionDto $dto, User $user, Season $season): array
    {
        // 1. upload image

        $dirname = $this->parameterBag->get('kernel.project_dir') . '/uploads/';
        
        do {
            $uniquePath = uniqid(more_entropy: true) . '.jpg';
            $absolutePath = $dirname . $uniquePath;
        } while($this->fs->exists($absolutePath));

        $tmpPath = $uniquePath . '.tmp';
        
        $newFile = $dto->image->move($dirname, $tmpPath);
        
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
        } catch (ImagickException $e) {
            return ['image_error'];
        } finally {
            $this->fs->remove($newFile);
        }

        // 2. create submmission and save it
        
        $submission = new Submission($user, $dto->activity, $season, $uniquePath, $dto->distance, $dto->elevation ?? 0);
        
        $this->submissionRepository->save($submission, true);

        return [];
    }

    private function setState(Submission $submission, bool $state): void
    {
        $submission->setReviewed(true);
        $submission->setAccepted($state);
        $this->submissionRepository->save($submission, true);
    }

    public function accept(Submission $submission): void
    {
        $this->profileCacheRepository->addCache($submission, false);
        $this->setState($submission, true);
    }

    public function reject(Submission $submission, string $message): void
    {
        $this->rejectedSubmissionMessageRepository->save(new RejectedSubmissionMessage($submission, $message));
        $this->setState($submission, false);
        
        $this->firebase->send(new VytrvalecNotification($submission->getUser(), $message));
        $this->mailer->send(new VytrvalecEmail($submission->getUser(), new SubmissionRejectedEmailTemplate($submission, $message)));
    }

    public function delete(Submission $submission): void
    {
        $this->submissionRepository->remove($submission, true);
    }
}
