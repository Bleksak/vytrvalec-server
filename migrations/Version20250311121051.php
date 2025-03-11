<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250311121051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add resutls and outliers to cache json';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            UPDATE cache
            SET data = JSON_OBJECT("results", JSON_EXTRACT(data, "$"), "outliers", JSON_ARRAY())
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            UPDATE cache
            SET data = JSON_EXTRACT(data, "$.results")
        ');
    }
}
