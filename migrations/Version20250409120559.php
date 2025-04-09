<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20250409120559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image and website to charity';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('charity');
        $table->addColumn('image', Types::STRING)
            ->setNotnull(false)
            ->setLength(512);

        $table->addColumn('website', Types::STRING)
            ->setNotnull(false)
            ->setLength(512);
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('charity');
        $table->dropColumn('image');
        $table->dropColumn('website');
    }
}
