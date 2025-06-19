<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20250619003445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('user');
        $table->addColumn('locale', Types::STRING, ['length' => 8])->setDefault('cs_CZ');
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('user');
        $table->dropColumn('locale');
    }
}
