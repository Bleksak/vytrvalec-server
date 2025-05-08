<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250507230407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix small inconsistencies';
    }

    public function up(Schema $schema): void
    {
        $submissionTable = $schema->getTable('submission');
        $submissionTable->getColumn('accepted')->setNotnull(true);
    }

    public function down(Schema $schema): void
    {
        $submissionTable = $schema->getTable('submission');
        $submissionTable->getColumn('accepted')->setNotnull(false);
    }
}
