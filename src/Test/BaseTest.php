<?php

namespace App\Test;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

class BaseTest extends WebTestCase
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
            self::$application = new Application(self::createKernel([]));

            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

}