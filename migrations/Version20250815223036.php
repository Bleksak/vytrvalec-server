<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20250815223036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add color to faculty table';
    }

    public function up(Schema $schema): void
    {
        $schema->getTable('faculty')->addColumn('color', Types::STRING)->setLength(9)->setNotnull(true)->setDefault('');
    }

    public function down(Schema $schema): void
    {
        $schema->getTable('faculty')->dropColumn('color');
    }
}
