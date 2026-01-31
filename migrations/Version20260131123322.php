<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260131123322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add bool is_test column to season';
    }

    public function up(Schema $schema): void
    {
        $schema
            ->getTable('season')
            ->addColumn('is_test', Types::BOOLEAN)
            ->setNotnull(true)
            ->setDefault(false);
    }

    public function down(Schema $schema): void
    {
        $schema->getTable('season')->dropColumn('is_test');
    }
}
