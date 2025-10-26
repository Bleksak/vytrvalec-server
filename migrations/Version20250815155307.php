<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20250815155307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add faculty_translation table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('faculty_translation');

        $table->addColumn('locale', Types::STRING)->setLength(6)->setNotnull(true);
        $table->addColumn('name', Types::STRING)->setLength(255)->setNotnull(true);
        $table->addColumn('faculty_id', Types::INTEGER)->setNotnull(true);

        $table->addPrimaryKeyConstraint(
            PrimaryKeyConstraint::editor()
                ->setQuotedName('pk_faculty_translation')
                ->setQuotedColumnNames('locale', 'faculty_id')
                ->create()
        );

        $table->addForeignKeyConstraint(
            'faculty',
            ['faculty_id'],
            ['id'],
            name: 'fk_faculty_id'
        );

        $schema->getTable('faculty')->dropColumn('name');
    }

    public function down(Schema $schema): void
    {
        $schema->getTable('faculty')->addColumn('name', Types::STRING)->setLength(255)->setNotnull(true);
        $schema->dropTable('faculty_translation');
    }
}
