<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Bridge\Doctrine\Types\UuidType;

final class Version20250410102746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('image');

        $table->addColumn('uuid', UuidType::NAME)->setLength(512);
        $table->addColumn('path', Types::STRING)->setLength(512);
        $table->addColumn('uploaded_at', Types::DATETIME_IMMUTABLE);
        $table->addColumn('used_at', Types::DATETIME_IMMUTABLE)
            ->setDefault(null)
            ->setNotnull(false);

        $table->setPrimaryKey(['uuid'], 'pk_hash');
        $table->addIndex(['used_at'], 'idx_used_at');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('image');
    }
}
