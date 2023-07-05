<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230704225246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE faculty_summary (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, faculty_id INT NOT NULL, distance INT NOT NULL, elevation INT NOT NULL, INDEX IDX_8F3BB0394EC001D1 (season_id), INDEX IDX_8F3BB039680CAB68 (faculty_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE faculty_summary ADD CONSTRAINT FK_8F3BB0394EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE faculty_summary ADD CONSTRAINT FK_8F3BB039680CAB68 FOREIGN KEY (faculty_id) REFERENCES faculty (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE faculty_summary DROP FOREIGN KEY FK_8F3BB0394EC001D1');
        $this->addSql('ALTER TABLE faculty_summary DROP FOREIGN KEY FK_8F3BB039680CAB68');
        $this->addSql('DROP TABLE faculty_summary');
    }
}
