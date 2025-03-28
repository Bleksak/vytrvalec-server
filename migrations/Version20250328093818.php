<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20250328093818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add email unsubscribe hash to the user table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('user');

        $table
            ->addColumn('email_unsubscribe_hash', Types::STRING)
            ->setNotnull(false);

        $table->addIndex(['email_unsubscribe_hash'], 'email_unsubscribe_hash');
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('user');
        $table->dropIndex('email_unsubscribe_hash');
        $table->dropColumn('email_unsubscribe_hash');
    }
}
