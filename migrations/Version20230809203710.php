<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230809203710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX date_index ON submission');
        $this->addSql('CREATE INDEX week_index ON submission (week)');
        $this->addSql('ALTER TABLE user ADD expo_token VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP expo_token');
        $this->addSql('DROP INDEX week_index ON submission');
        $this->addSql('CREATE INDEX date_index ON submission (date)');
    }
}
