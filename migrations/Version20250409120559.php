<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Bridge\Doctrine\Types\UuidType;

final class Version20250409120559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image and website to charity';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('charity');
        $table->addColumn('image_uuid', UuidType::NAME)
            ->setNotnull(false);

        $table->addColumn('website', Types::STRING)
            ->setNotnull(false)
            ->setLength(512);

        $table->addForeignKeyConstraint('image', ['image_uuid'], ['uuid']);
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('charity');
        $table->dropColumn('image_uuid');
        $table->dropColumn('website');
    }
}
