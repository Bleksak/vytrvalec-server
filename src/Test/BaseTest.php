<?php

namespace App\Test;

use App\Entity\Charity;
use App\Entity\Faculty;
use App\Entity\User;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

class BaseTest extends WebTestCase
{
    protected static ?Application $application = null;
    protected KernelBrowser $client;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:schema:update --force');
        self::runCommand('doctrine:fixtures:load --no-interaction');

        $this->client = self::createClient();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->client->getContainer()->get(EntityManagerInterface::class);
    }

    protected function createUser(string $email, string $password, array $roles = []): void
    {
        $faculty = $this->getEntityManager()->getRepository(Faculty::class)->findOneBy(['shortcut' => 'FAV']);

        $this->client->request('POST', '/api/user', [
            "email" => $email,
            "password" => $password,
            "first_name" => 'string',
            "last_name" => 'string',
            "faculty" => $faculty->getId(),
        ]);

        if(!empty($roles)) {
            $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => $email]);
            $user->setRoles($roles);

            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        }
    }

    protected function loginUser(string $email, string $password): void
    {
        $this->client->request('POST', '/api/user/login', [
            'email' => $email,
            'password' => $password
        ]);
    }

    protected function grantRole(array $role = []): void
    {
        $testUser = 'TestUser@TestUser.com';
        $testPassword = 'TestingPassword45511';

        $this->createUser($testUser, $testPassword, $role);
        $this->loginUser($testUser, $testPassword);
    }

    protected function makeCharity(): void
    {
        $this->client->jsonRequest('POST', '/api/charity', [
            'name' => 'CharityTest',
            'description' => 'CharityTestDescription',
        ]);
    }

    protected function makeSeason(): void
    {
        $this->makeCharity();
        $repository = $this->getEntityManager()->getRepository(Charity::class);
        $charity = $repository->findOneBy(['name' => 'CharityTest']);

        $now = new DateTimeImmutable();
        $now = $now->setTime(0, 0, 0);

        $end = $now->add(new DateInterval('P4W'));

        $this->client->jsonRequest('POST', '/api/season', [
            'start' => $now->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'charity' => $charity->getId(),
        ]);
    }

    /**
     * @throws \Exception
     */
    protected static function runCommand($command): void
    {
        $command = sprintf('%s --quiet', $command);
        self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication(): ?Application
    {
        if (self::$application === null) {
            self::$application = new Application(self::createKernel([]));

            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

}
