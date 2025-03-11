<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250311114535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Converts all tables to utf8mb4';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER DATABASE '.$schema->getQuotedName($this->platform).' DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        foreach ($schema->getTables() as $table) {
            $this->addSql('ALTER TABLE '.$table->getQuotedName($this->platform).' DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            $this->addSql('ALTER TABLE '.$table->getQuotedName($this->platform).' CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }

    }

    public function down(Schema $schema): void
    {
    }
}
