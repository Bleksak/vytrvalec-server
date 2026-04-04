<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\DataFixtures\FacultyFixtures;
use Behat\Hook\BeforeScenario;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * @internal
 */
trait DatabaseContextTrait
{
    protected EntityManagerInterface $entityManager;

    #[BeforeScenario]
    public function prepareDatabase(): void
    {
        $schemaTool = new SchemaTool($this->entityManager);

        $metadata = $this->entityManager
            ->getMetadataFactory()
            ->getAllMetadata();

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        $fixture = new FacultyFixtures();
        $fixture->load($this->entityManager);
    }
}
