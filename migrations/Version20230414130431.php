<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230414130431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE submission ADD season_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL, ADD accepted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF34EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_DB055AF34EC001D1 ON submission (season_id)');
        $this->addSql('CREATE INDEX IDX_DB055AF3A76ED395 ON submission (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF34EC001D1');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF3A76ED395');
        $this->addSql('DROP INDEX IDX_DB055AF34EC001D1 ON submission');
        $this->addSql('DROP INDEX IDX_DB055AF3A76ED395 ON submission');
        $this->addSql('ALTER TABLE submission DROP season_id, DROP user_id, DROP accepted');
    }
}
