<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250331180049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Allow User email to be nullable for user deletion process';
    }

    public function up(Schema $schema): void
    {
        $schema->getTable('user')->getColumn('email')->setNotnull(false);
    }

    public function down(Schema $schema): void
    {
        $schema->getTable('user')->getColumn('email')->setNotnull(true);
    }
}
