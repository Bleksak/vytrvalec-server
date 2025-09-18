<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Name\OptionallyQualifiedName;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20250904121043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add a faculty -> parent mapping table for seasons';
    }

    public function up(Schema $schema): void
    {
        $table = Table::editor()
            ->setQuotedName('faculty_mapping')
            ->addColumn(
                Column::editor()
                    ->setQuotedName('season_id')
                    ->setType(Type::getType(Types::INTEGER))
                    ->setNotNull(true)
                    ->create(),
            )
            ->addColumn(
                Column::editor()
                    ->setQuotedName('faculty_id')
                    ->setType(Type::getType(Types::INTEGER))
                    ->setNotNull(true)
                    ->create(),
            )
            ->addColumn(
                Column::editor()
                    ->setQuotedName('parent_id')
                    ->setType(Type::getType(Types::INTEGER))
                    ->setNotNull(false)
                    ->create(),
            )
            ->setPrimaryKeyConstraint(
                PrimaryKeyConstraint::editor()
                    ->setQuotedColumnNames('season_id', 'faculty_id')
                    ->setQuotedName('pk_season_id_faculty_id')
                    ->create(),
            )
            ->setForeignKeyConstraints(
                ForeignKeyConstraint::editor()
                    ->setQuotedName('fk_season')
                    ->setQuotedReferencingColumnNames('season_id')
                    ->setQuotedReferencedColumnNames('id')
                    ->setReferencedTableName(OptionallyQualifiedName::quoted('season'))
                    ->create(),
                ForeignKeyConstraint::editor()
                    ->setQuotedName('fk_faculty')
                    ->setQuotedReferencingColumnNames('faculty_id')
                    ->setQuotedReferencedColumnNames('id')
                    ->setReferencedTableName(OptionallyQualifiedName::quoted('faculty'))
                    ->create(),
                ForeignKeyConstraint::editor()
                    ->setQuotedName('fk_parent')
                    ->setQuotedReferencingColumnNames('parent_id')
                    ->setQuotedReferencedColumnNames('id')
                    ->setReferencedTableName(OptionallyQualifiedName::quoted('faculty'))
                    ->create(),
            )
            ->create();

        $newSchema = new Schema([$table]);

        $platform = $this->connection->getDatabasePlatform();
        $statements = $newSchema->toSql($platform);

        foreach ($statements as $statement) {
            $this->addSql($statement);
        }
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('faculty_mapping');
    }
}
