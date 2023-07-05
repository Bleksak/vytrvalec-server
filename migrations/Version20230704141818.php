<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230704141818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE season ADD end DATE NOT NULL');
        $this->addSql('CREATE INDEX date_index ON season (start)');
        $this->addSql('ALTER TABLE submission ADD activity_id INT NOT NULL, ADD date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', CHANGE season_id season_id INT NOT NULL, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF381C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('CREATE INDEX IDX_DB055AF381C06096 ON submission (activity_id)');
        $this->addSql('CREATE INDEX date_index ON submission (date)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX date_index ON season');
        $this->addSql('ALTER TABLE season DROP end');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF381C06096');
        $this->addSql('DROP INDEX IDX_DB055AF381C06096 ON submission');
        $this->addSql('DROP INDEX date_index ON submission');
        $this->addSql('ALTER TABLE submission DROP activity_id, DROP date, CHANGE season_id season_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
    }
}
