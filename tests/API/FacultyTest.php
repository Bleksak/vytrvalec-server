<?php

namespace App\Tests\API;

use App\DataFixtures\FacultyFixtures;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

class FacultyTest extends WebTestCase
{
    protected static ?Application $application = null;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:schema:update --force');
        self::runCommand('doctrine:fixtures:load --no-interaction');
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
//            $client = static::createClient();
            self::$application = new Application(self::createKernel([]));

            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    public function testFetch(): void
    {
        $client = static::createClient();
        $facultyFixture = new FacultyFixtures();
        $crawler = $client->request('GET', '/api/faculty/list');

        $this->assertResponseIsSuccessful();
        $this->assertSame($client->getResponse()->headers->get('Content-Type'), 'application/json');

//        $decoded = json_decode($client->getResponse()->getContent());
//
//        $this->assertCount(9, $decoded);
    }


}