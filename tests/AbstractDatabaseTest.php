<?php

declare(strict_types=1);

namespace Tests;

use Doctrine\ORM\EntityManagerInterface;

/** @internal */
abstract class AbstractDatabaseTest extends \PHPUnit\Framework\TestCase
{
    protected static ?EntityManagerInterface $em = null;

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (static::$em === null) {
            static::$em = static::getEntityManager();
        }
    }

    protected static function getEntityManager(): EntityManagerInterface
    {
        $kernel = new \App\Kernel('test', true);
        $kernel->boot();

        return $kernel->getContainer()->get(EntityManagerInterface::class);
    }

    protected function clearDatabase(): void
    {
        try {
            $em = static::$em ?? static::getEntityManager();
            $connection = $em->getConnection();
            $schemaManager = $connection->createSchemaManager();

            foreach ($schemaManager->introspectTables() as $table) {
                $connection->executeStatement(
                    'DELETE FROM ' . $table->getObjectName()->toString(),
                );
            }
        } catch (\Throwable $e) {
            // @mago-expect: Database may not exist during initial test runs
            throw new \Exception('Failed to clear database', 0, $e);
        }
    }
}
