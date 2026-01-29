<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\SeasonRepository;
use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'results:find-outliers',
    description: 'Add a short description for your command',
)]
final class ResultsFindOutliersCommand
{
    public function __construct(
        private readonly SubmissionRepository $submissionRepository,
        private readonly SeasonRepository $seasonRepository,
        private readonly UserRepository $userRepository,
    ) {}

    public function __invoke(SymfonyStyle $io, #[Argument] int $seasonId): int
    {
        $season = $this->seasonRepository->find($seasonId);

        if ($season === null) {
            $io->error('Sezona nebyla nalezena');

            return Command::FAILURE;
        }

        $topThree = $this->submissionRepository->findOutliers($season);

        foreach ($topThree as $outlier) {
            $io->writeln('Aktivita ID: ' . $outlier->activityId);

            foreach ($outlier->results as $result) {
                \assert(\is_int($result->user));

                $user = $this->userRepository->find($result->user);

                if ($user === null) {
                    $io->error('Failed to find user with ID ' . $result->user);
                    return Command::FAILURE;
                }

                $io->writeln(
                    $user->getFirstName() . ' ' . $user->getLastName(),
                );

                $io->writeln('Fakulta: ' . $result->facultyId);
                $io->writeln('Celkove m: ' . $result->value);
            }
        }

        return Command::SUCCESS;
    }
}
