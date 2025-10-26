<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20250817181721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add charity_translation table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('charity_translation');
        $table->addColumn('locale', Types::STRING)->setLength(6)->setNotnull(true);
        $table->addColumn('charity_id', Types::INTEGER)->setNotnull(true);
        $table->addColumn('name', Types::STRING)->setLength(255)->setNotnull(true);
        $table->addColumn('description', Types::STRING)->setLength(10000)->setNotnull(true);

        $table->addPrimaryKeyConstraint(
            PrimaryKeyConstraint::editor()
                ->setQuotedName('pk_charity_translation')
                ->setQuotedColumnNames('locale', 'charity_id')
                ->create()
        );

        $table->addForeignKeyConstraint(
            'charity',
            ['charity_id'],
            ['id'],
            name: 'fk_charity_id'
        );

        $charityTable = $schema->getTable('charity');
        $charityTable->dropColumn('name');
        $charityTable->dropColumn('description');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('charity_translation');

        $charityTable = $schema->getTable('charity');
        $charityTable->addColumn('name', Types::STRING)->setLength(255)->setNotnull(true);
        $charityTable->addColumn('description', Types::STRING)->setLength(10000)->setNotnull(true);
    }
}
