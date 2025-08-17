<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\SeasonRepository;
use App\Repository\SubmissionRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'results:find-outliers', description: 'Add a short description for your command')]
final class ResultsFindOutliersCommand extends Command
{
    public function __construct(
        private readonly SubmissionRepository $submissionRepository,
        private readonly SeasonRepository $seasonRepository,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('season_id', InputArgument::REQUIRED, 'Season ID');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $seasonId = $input->getArgument('season_id');

        $season = $this->seasonRepository->find($seasonId);

        if ($season === null) {
            $io->error('Sezona nebyla nalezena');

            return Command::FAILURE;
        }

        $topThree = $this->submissionRepository->findOutliers($season, shouldAnonymize: false);

        foreach ($topThree as $outlier) {
            $io->writeln('Aktivita ID: ' . $outlier->activityId);

            foreach ($outlier->results as $result) {
                assert($result->user->lastName !== null, 'User should not be anonymized');

                $io->writeln($result->user->firstName . ' ' . $result->user->lastName);
                $io->writeln('Fakulta: ' . $result->facultyId);
                $io->writeln('Celkove m: ' . $result->value);
            }
        }

        return Command::SUCCESS;
    }
}
