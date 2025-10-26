<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\SeasonRepository;
use App\Repository\SubmissionRepository;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'results:find-outliers', description: 'Add a short description for your command')]
final class ResultsFindOutliersCommand
{
    public function __construct(
        private readonly SubmissionRepository $submissionRepository,
        private readonly SeasonRepository $seasonRepository,
    ) {
    }

    public function __invoke(SymfonyStyle $io, #[Argument] int $seasonId): int
    {
        $season = $this->seasonRepository->find($seasonId);

        if ($season === null) {
            $io->error('Sezona nebyla nalezena');

            return Command::FAILURE;
        }

        $topThree = $this->submissionRepository->findOutliers($season, shouldAnonymize: false);

        foreach ($topThree as $outlier) {
            $io->writeln('Aktivita ID: '.$outlier->activityId);

            foreach ($outlier->results as $result) {
                \assert($result->user->lastName !== null, 'User should not be anonymized');

                $io->writeln($result->user->firstName.' '.$result->user->lastName);
                $io->writeln('Fakulta: '.$result->facultyId);
                $io->writeln('Celkove m: '.$result->value);
            }
        }

        return Command::SUCCESS;
    }
}
