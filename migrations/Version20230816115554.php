<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230816115554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE extra_points (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, points INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faculty_cache (id INT AUTO_INCREMENT NOT NULL, faculty_id INT NOT NULL, activity_id INT NOT NULL, season_id INT NOT NULL, week INT NOT NULL, distance INT NOT NULL, elevation INT NOT NULL, INDEX IDX_5AC38379680CAB68 (faculty_id), INDEX IDX_5AC3837981C06096 (activity_id), INDEX IDX_5AC383794EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faculty_extra_points (id INT AUTO_INCREMENT NOT NULL, cache_id INT NOT NULL, user_id INT NOT NULL, extra_points_id INT NOT NULL, value INT NOT NULL, INDEX IDX_7708E4C3A45D650B (cache_id), INDEX IDX_7708E4C3A76ED395 (user_id), INDEX IDX_7708E4C32D458B39 (extra_points_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE faculty_cache ADD CONSTRAINT FK_5AC38379680CAB68 FOREIGN KEY (faculty_id) REFERENCES faculty (id)');
        $this->addSql('ALTER TABLE faculty_cache ADD CONSTRAINT FK_5AC3837981C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE faculty_cache ADD CONSTRAINT FK_5AC383794EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE faculty_extra_points ADD CONSTRAINT FK_7708E4C3A45D650B FOREIGN KEY (cache_id) REFERENCES faculty_cache (id)');
        $this->addSql('ALTER TABLE faculty_extra_points ADD CONSTRAINT FK_7708E4C3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE faculty_extra_points ADD CONSTRAINT FK_7708E4C32D458B39 FOREIGN KEY (extra_points_id) REFERENCES extra_points (id)');
        $this->addSql('ALTER TABLE profile_cache MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON profile_cache');
        $this->addSql('ALTER TABLE profile_cache DROP id');
        $this->addSql('ALTER TABLE profile_cache ADD PRIMARY KEY (user_id, activity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE faculty_cache DROP FOREIGN KEY FK_5AC38379680CAB68');
        $this->addSql('ALTER TABLE faculty_cache DROP FOREIGN KEY FK_5AC3837981C06096');
        $this->addSql('ALTER TABLE faculty_cache DROP FOREIGN KEY FK_5AC383794EC001D1');
        $this->addSql('ALTER TABLE faculty_extra_points DROP FOREIGN KEY FK_7708E4C3A45D650B');
        $this->addSql('ALTER TABLE faculty_extra_points DROP FOREIGN KEY FK_7708E4C3A76ED395');
        $this->addSql('ALTER TABLE faculty_extra_points DROP FOREIGN KEY FK_7708E4C32D458B39');
        $this->addSql('DROP TABLE extra_points');
        $this->addSql('DROP TABLE faculty_cache');
        $this->addSql('DROP TABLE faculty_extra_points');
        $this->addSql('ALTER TABLE profile_cache ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
