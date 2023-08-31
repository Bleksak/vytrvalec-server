<?php

namespace App\Command;

use App\Repository\FacultyExtraPointsRepository;
use App\Repository\SeasonRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'RecalculateExtraPoints',
    description: 'Add a short description for your command',
)]
class RecalculateExtraPointsCommand extends Command
{

    public function __construct(private readonly FacultyExtraPointsRepository $extraPointsRepository, private readonly SeasonRepository $seasonRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $season = $this->seasonRepository->getLast();
        if($season === null) {
            return Command::SUCCESS;
        }

        $minWeek = 0;
        $maxWeek = 3;

        $week = max($minWeek, min(intdiv((new \DateTimeImmutable('now'))->diff($season->getStart())->d, 7), $maxWeek));
        $this->extraPointsRepository->constructForWeek($season, $week);

        $io->success('Successfully calculated extra points');

        return Command::SUCCESS;
    }
}
