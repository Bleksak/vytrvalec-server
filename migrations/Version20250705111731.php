<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Bridge\Doctrine\Types\UuidType;

final class Version20250705111731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add activity icon image';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('activity');
        $table->addColumn('icon_uuid', UuidType::NAME)
            ->setNotnull(false);
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('activity');
        $table->dropColumn('icon_uuid');
    }
}
