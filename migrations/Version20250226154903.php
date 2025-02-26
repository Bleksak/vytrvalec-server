<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20250226154903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds an accepted_gdpr field to user class';
    }

    public function up(Schema $schema): void
    {
        $schema
            ->getTable('user')
            ->addColumn('accepted_gdpr', Types::BOOLEAN)
            ->setDefault(null)
            ->setNotnull(false);
    }

    public function down(Schema $schema): void
    {
        $schema
            ->getTable('user')
            ->dropColumn('accepted_gdpr');
    }
}
