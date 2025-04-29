<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20250413141312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop image from submission';
    }

    public function up(Schema $schema): void
    {
        $schema
            ->getTable('submission')
            ->dropColumn('image');
    }

    public function down(Schema $schema): void
    {
        $schema
            ->getTable('submission')
            ->addColumn('image', Types::STRING)
            ->setLength(512);
    }
}
