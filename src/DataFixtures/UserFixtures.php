<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\FacultyRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly FacultyRepository $facultyRepository,
    ) {}

    #[Override]
    public function load(ObjectManager $manager): void
    {
        $faculty = $this->facultyRepository->find(1);

        \assert($faculty !== null);

        $user = new User(
            email: 'existing@test.com',
            firstName: 'Jiri',
            lastName: 'Velek',
            faculty: $faculty,
            anonymize: false,
        );

        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'VeryStrongPassword123@!',
        ));

        $manager->persist($user);
        $manager->flush();
    }

    #[Override]
    public function getDependencies(): array
    {
        return [FacultyFixtures::class];
    }
}
