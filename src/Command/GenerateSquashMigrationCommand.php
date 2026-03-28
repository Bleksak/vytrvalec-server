<?php

declare(strict_types=1);

namespace App\Command;

use App\Trait\DoctrineNamespaceTrait;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'migrations:squash',
    description: 'Generate squash migration for sqlite and mysql databases',
)]
final class GenerateSquashMigrationCommand
{
    use DoctrineNamespaceTrait;

    public function __construct(
        private EntityManagerInterface $em,
        private DependencyFactory $dependencyFactory,
    ) {}

    private function generateAddSqlCommand(string $query): string
    {
        return <<<SQL
            \$this->addSql('{$query}');
            SQL;
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $mysql = new MySQLPlatform();
        $sqlite = new SQLitePlatform();

        $metadata = $this->em->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($this->em);
        $schema = $schemaTool->getSchemaFromMetadata($metadata);

        $configuration = $this->dependencyFactory->getConfiguration();
        $dirs = $configuration->getMigrationDirectories();

        $migrationGenerator = $this->dependencyFactory->getMigrationGenerator();

        $namespace = $this->getDoctrineNamespace($dirs);

        $fqcn = $this->dependencyFactory
            ->getClassNameGenerator()
            ->generateClassName($namespace);

        $sqliteUp = \implode("\n\t", \array_map(
            self::generateAddSqlCommand(...),
            $schema->toSql($sqlite),
        ));

        $sqliteDown = \implode("\n\t", \array_map(
            self::generateAddSqlCommand(...),
            $schema->toDropSql($sqlite),
        ));

        $mysqlUp = \implode("\n\t", \array_map(
            self::generateAddSqlCommand(...),
            $schema->toSql($mysql),
        ));

        $mysqlDown = \implode("\n\t", \array_map(
            self::generateAddSqlCommand(...),
            $schema->toDropSql($mysql),
        ));

        $sqliteUpContent = <<<SQL
            if(\$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SQLitePlatform) {
                {$sqliteUp}
            }
            SQL;

        $mysqlUpContent = <<<SQL
            if(\$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQLPlatform) {
                {$mysqlUp}
            }
            SQL;

        $sqliteDownContent = <<<SQL
            if(\$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SQLitePlatform) {
                {$sqliteDown}
            }
            SQL;

        $mysqlDownContent = <<<SQL
            if(\$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQLPlatform) {
                {$mysqlDown}
            }
            SQL;

        $combinedUp = $sqliteUpContent . "\n" . $mysqlUpContent;
        $combinedDown = $sqliteDownContent . "\n" . $mysqlDownContent;

        $path = $migrationGenerator->generateMigration(
            $fqcn,
            $combinedUp,
            $combinedDown,
        );

        $io->success(\sprintf('Migration generated at %s', $path));

        return Command::SUCCESS;
    }
}
