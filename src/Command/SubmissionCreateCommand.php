<?php

namespace App\Command;

use App\Entity\Season;
use App\Entity\Submission;
use App\Repository\ActivityRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'submission:create',
    description: 'Add a short description for your command',
)]
class SubmissionCreateCommand extends Command
{
    private ?Season $season;

    public function __construct(
        private SeasonRepository $seasonRepository, 
        private ActivityRepository $activityRepository, 
        private SubmissionRepository $submissionRepository, 
        private UserRepository $userRepository,
    )
    {
        parent::__construct();
        $this->season = $seasonRepository->getCurrent();
    }

    protected function configure(): void
    {

    }

    private function randomDateInRange(DateTime $start, DateTime $end): DateTimeImmutable
    {
        $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
        $randomDate = new DateTime();
        $randomDate->setTimestamp($randomTimestamp);
        return DateTimeImmutable::createFromMutable($randomDate);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if($this->season === null) {
            $io->error('No season running');
            return Command::FAILURE;
        }

        $start = $this->season->getStart();
        $end = $this->season->getEnd();
        $date = $this->randomDateInRange($start, $end);

        $activityName = $io->choice('Which activity?', $this->activityRepository->findForSelect());
        $userEmail = $io->ask('User email: ');
        $user = $this->userRepository->findOneBy(['email' => $userEmail]);

        $submission = new Submission();

        $submission->setSeason($this->season);
        $submission->setDate($date);
        $submission->setImage('');
        $submission->setReviewed(true);
        $submission->setAccepted(true);
        $submission->setActivity($this->activityRepository->findOneBy(['name' => $activityName]));
        $submission->setUser($user);
        $submission->setDistance(rand(1000, 10000));
        $submission->setElevation(rand(1000, 10000));
        $submission->calculateWeek();

        $this->submissionRepository->save($submission, true);

        $io->success('New submission created');

        return Command::SUCCESS;
    }
}
