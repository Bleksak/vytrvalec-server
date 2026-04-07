<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\DataFixtures\FacultyFixtures;
use App\DataFixtures\UserFixtures;
use Behat\Hook\AfterScenario;
use Behat\Hook\BeforeScenario;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Psr\Container\ContainerInterface;

/**
 * @internal
 */
trait DatabaseContextTrait
{
    protected EntityManagerInterface $entityManager;
    protected ContainerInterface $container;
    private static ?bool $inMemoryDb = null;

    private static function isInitialized(EntityManagerInterface $em): bool
    {
        $connection = $em->getConnection();

        $schemaManager = $connection->createSchemaManager();
        return \count($schemaManager->introspectTables()) > 0;
    }

    /** @param list<FixtureInterface> $fixtures */
    private static function prepareDatabase(
        EntityManagerInterface $entityManager,
        array $fixtures,
    ): void {
        if (self::isInitialized($entityManager)) {
            return;
        }

        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->createSchema($metadata);

        foreach ($fixtures as $fixture) {
            $fixture->load($entityManager);
        }

        $entityManager
            ->getConnection()
            ->executeStatement('PRAGMA foreign_keys = ON');
    }

    #[BeforeScenario]
    public function startTransaction(): void
    {
        self::prepareDatabase($this->entityManager, [
            $this->container->get(FacultyFixtures::class),
            $this->container->get(UserFixtures::class),
        ]);

        $this->entityManager->beginTransaction();
    }

    #[AfterScenario]
    public function rollbackTransaction(): void
    {
        $this->entityManager->rollback();
    }
}
