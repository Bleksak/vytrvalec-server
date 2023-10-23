<?php

namespace App\Command;

use App\Entity\Charity;
use App\Entity\Season;
use App\Repository\CharityRepository;
use App\Repository\SeasonRepository;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'season:create',
    description: 'Creates a new season starting today',
)]
class CreateSeasonCommand extends Command
{
    
    public function __construct(private SeasonRepository $seasonRepository, private CharityRepository $charityRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('start', null, InputOption::VALUE_REQUIRED, 'Start date')
            ->addOption('end', null, InputOption::VALUE_REQUIRED, 'End date')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $start = $input->getOption('start') ?? new DateTimeImmutable();
        $end = $input->getOption('end') ?? $start->add(new \DateInterval('P3W'));

        $charityName = $io->ask('Charity name: ');
        $charityDescription = $io->ask('Charity description: ');

        $charity = new Charity($charityName, $charityDescription);

        $season = new Season($start, $end, $charity);

        $this->seasonRepository->save($season, true);

        $io->success('Season successfully created');

        return Command::SUCCESS;
    }
}
