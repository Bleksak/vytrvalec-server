<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20250303123232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add parent to faculties';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('faculty');
        $table->addColumn('parent_id', Types::INTEGER)
            ->setNotnull(false);

        $table->addForeignKeyConstraint(
            $table,
            ['parent_id'],
            ['id'],
            ['onUpdate' => 'CASCADE'],
            'parent_id'
        );
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('faculty');
        $table->removeForeignKey('parent');

        $table->dropColumn('parent');
    }
}
