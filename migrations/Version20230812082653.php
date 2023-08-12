<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230812082653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE profile_cache (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, activity_id INT NOT NULL, distance INT NOT NULL, elevation INT NOT NULL, INDEX IDX_B693F435A76ED395 (user_id), INDEX IDX_B693F43581C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE profile_cache ADD CONSTRAINT FK_B693F435A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE profile_cache ADD CONSTRAINT FK_B693F43581C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE rejected_submission_message DROP FOREIGN KEY FK_2FA1DFE8BF396750');
        $this->addSql('DROP TABLE rejected_submission_message');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rejected_submission_message (id INT NOT NULL, message VARCHAR(512) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE rejected_submission_message ADD CONSTRAINT FK_2FA1DFE8BF396750 FOREIGN KEY (id) REFERENCES submission (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE profile_cache DROP FOREIGN KEY FK_B693F435A76ED395');
        $this->addSql('ALTER TABLE profile_cache DROP FOREIGN KEY FK_B693F43581C06096');
        $this->addSql('DROP TABLE profile_cache');
    }
}
