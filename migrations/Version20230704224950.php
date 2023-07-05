<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230704224950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_summary (id INT AUTO_INCREMENT NOT NULL, season_id INT DEFAULT NULL, user_id INT NOT NULL, distance INT NOT NULL, elevation INT NOT NULL, INDEX IDX_962578694EC001D1 (season_id), UNIQUE INDEX UNIQ_96257869A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_summary ADD CONSTRAINT FK_962578694EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE user_summary ADD CONSTRAINT FK_96257869A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE season ADD summaries_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA9D7545631 FOREIGN KEY (summaries_id) REFERENCES user_summary (id)');
        $this->addSql('CREATE INDEX IDX_F0E45BA9D7545631 ON season (summaries_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE season DROP FOREIGN KEY FK_F0E45BA9D7545631');
        $this->addSql('ALTER TABLE user_summary DROP FOREIGN KEY FK_962578694EC001D1');
        $this->addSql('ALTER TABLE user_summary DROP FOREIGN KEY FK_96257869A76ED395');
        $this->addSql('DROP TABLE user_summary');
        $this->addSql('DROP INDEX IDX_F0E45BA9D7545631 ON season');
        $this->addSql('ALTER TABLE season DROP summaries_id');
    }
}
