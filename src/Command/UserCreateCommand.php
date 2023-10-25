<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\FacultyRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'user:create',
    description: 'Add a short description for your command',
)]
class UserCreateCommand extends Command
{
    public function __construct(private UserRepository $userRepository, private FacultyRepository $facultyRepository, private UserPasswordHasherInterface $hasher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        // $this
        //     ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        //     ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        // ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $io->ask('Email: ');
        $password = $io->askHidden('Password: ');
        $faculty = $io->choice('Select the users\' faculty: ', $this->facultyRepository->findForSelect());
        $firstName = '';
        $lastName = '';
        
        $role = $io->choice('Select the users\' role: ', ['ROLE_STAFF', 'ROLE_USER']);

        $user = new User($email, $password, $firstName, $lastName, $faculty, [$role]);

        $this->userRepository->save($user, true);
        $io->success('User successfully created');

        return Command::SUCCESS;
    }
}
