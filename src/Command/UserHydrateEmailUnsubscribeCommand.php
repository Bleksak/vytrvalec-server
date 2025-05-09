<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'user:hydrate-email-unsubscribe',
    description: 'Hydrates email unsubscribe hash for all users',
)]
final class UserHydrateEmailUnsubscribeCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->userRepository->findAll() as $user) {
            if ($user->getEmailUnsubscribeHash() === null) {
                $user->setEmailUnsubscribeHash(bin2hex(random_bytes(90)));
                $this->userRepository->save($user);
            }
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
