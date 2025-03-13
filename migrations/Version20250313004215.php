<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20250313004215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add the mailing field to the User entity';
    }

    public function up(Schema $schema): void
    {
        $schema->getTable('user')
            ->addColumn('mailing', Types::BOOLEAN)
            ->setNotnull(true)
            ->setDefault(true);
    }

    public function down(Schema $schema): void
    {
        $schema->getTable('user')
            ->dropColumn('mailing');
    }
}
