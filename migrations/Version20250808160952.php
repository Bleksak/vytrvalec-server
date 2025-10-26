<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20250808160952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds activity_translation table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('activity_translation');

        $table->addColumn('locale', Types::STRING)->setLength(6);
        $table->addColumn('activity_id', Types::INTEGER)->setUnsigned(false);
        $table->addColumn('name', Types::STRING)->setLength(255)->setNotnull(true);

        $table->addPrimaryKeyConstraint(
            PrimaryKeyConstraint::editor()
                ->setQuotedColumnNames('locale', 'activity_id')
                ->setQuotedName('pk_locale_activity')
                ->create()
        );

        $table->addForeignKeyConstraint(
            'activity',
            ['activity_id'],
            ['id'],
            name: 'fk_activity_id',
        );

        $schema->getTable('activity')->dropColumn('name');
    }

    public function down(Schema $schema): void
    {
        $schema->getTable('activity')->addColumn('name', Types::STRING)->setLength(255)->setNotnull(true);
        $schema->dropTable('activity_translation');
    }
}
