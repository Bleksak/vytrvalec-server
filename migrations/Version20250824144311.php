<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20250824144311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add original_mime_type column to image table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('image');
        $table->addColumn('original_mime_type', Types::STRING)->setLength(32)->setNotnull(false);
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('image');
        $table->dropColumn('original_mime_type');
    }
}
