<?php

namespace App\Action;

use App\Entity\RejectedSubmissionMessage;
use App\Entity\Submission;
use App\Notifications\EmailTemplate\SubmissionRejectedEmailTemplate;
use App\Notifications\Firebase\Firebase;
use App\Notifications\VytrvalecEmail;
use App\Notifications\VytrvalecNotification;
use App\Repository\FacultyCacheRepository;
use App\Repository\ProfileCacheRepository;
use App\Repository\RejectedSubmissionMessageRepository;
use App\Repository\SubmissionRepository;
use App\Repository\UserCacheRepository;
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
    )
    {
    }

    private function setState(Submission $submission, bool $state): void
    {
        $submission->setReviewed(true);
        $submission->setAccepted($state);
        $this->submissionRepository->save($submission, true);
    }

    public function accept(Submission $submission): void
    {
        $this->profileCacheRepository->addCache($submission, true);
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
