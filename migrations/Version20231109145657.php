<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231109145657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, min_elevation INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE charity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(10000) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE extra_points (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, points INT NOT NULL, week INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faculty (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, shortcut VARCHAR(10) NOT NULL, visible TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faculty_cache (id INT AUTO_INCREMENT NOT NULL, faculty_id INT NOT NULL, activity_id INT NOT NULL, season_id INT NOT NULL, week INT NOT NULL, distance INT NOT NULL, elevation INT NOT NULL, INDEX IDX_5AC38379680CAB68 (faculty_id), INDEX IDX_5AC3837981C06096 (activity_id), INDEX IDX_5AC383794EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faculty_extra_points (faculty_cache_id INT NOT NULL, user_id INT NOT NULL, extra_points_id INT NOT NULL, value INT NOT NULL, INDEX IDX_7708E4C32FA2CE37 (faculty_cache_id), INDEX IDX_7708E4C3A76ED395 (user_id), INDEX IDX_7708E4C32D458B39 (extra_points_id), PRIMARY KEY(faculty_cache_id, user_id, extra_points_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile_cache (user_id INT NOT NULL, activity_id INT NOT NULL, distance INT NOT NULL, elevation INT NOT NULL, INDEX IDX_B693F435A76ED395 (user_id), INDEX IDX_B693F43581C06096 (activity_id), PRIMARY KEY(user_id, activity_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rejected_submission_message (id INT NOT NULL, message VARCHAR(512) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, charity_id INT NOT NULL, start DATE NOT NULL, end DATE NOT NULL, INDEX IDX_F0E45BA9F5C97E37 (charity_id), INDEX date_index (start), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE submission (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, user_id INT NOT NULL, activity_id INT NOT NULL, accepted TINYINT(1) DEFAULT NULL, elevation BIGINT NOT NULL, distance BIGINT NOT NULL, reviewed TINYINT(1) NOT NULL, image VARCHAR(255) NOT NULL, week INT NOT NULL, date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', updated_at DATE NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_DB055AF34EC001D1 (season_id), INDEX IDX_DB055AF3A76ED395 (user_id), INDEX IDX_DB055AF381C06096 (activity_id), INDEX week_index (week), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, faculty_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, banned TINYINT(1) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649680CAB68 (faculty_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_cache (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, activity_id INT NOT NULL, season_id INT NOT NULL, week INT NOT NULL, distance INT NOT NULL, elevation INT NOT NULL, INDEX IDX_A38590BFA76ED395 (user_id), INDEX IDX_A38590BF81C06096 (activity_id), INDEX IDX_A38590BF4EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE faculty_cache ADD CONSTRAINT FK_5AC38379680CAB68 FOREIGN KEY (faculty_id) REFERENCES faculty (id)');
        $this->addSql('ALTER TABLE faculty_cache ADD CONSTRAINT FK_5AC3837981C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE faculty_cache ADD CONSTRAINT FK_5AC383794EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE faculty_extra_points ADD CONSTRAINT FK_7708E4C32FA2CE37 FOREIGN KEY (faculty_cache_id) REFERENCES faculty_cache (id)');
        $this->addSql('ALTER TABLE faculty_extra_points ADD CONSTRAINT FK_7708E4C3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE faculty_extra_points ADD CONSTRAINT FK_7708E4C32D458B39 FOREIGN KEY (extra_points_id) REFERENCES extra_points (id)');
        $this->addSql('ALTER TABLE profile_cache ADD CONSTRAINT FK_B693F435A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE profile_cache ADD CONSTRAINT FK_B693F43581C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE rejected_submission_message ADD CONSTRAINT FK_2FA1DFE8BF396750 FOREIGN KEY (id) REFERENCES submission (id)');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA9F5C97E37 FOREIGN KEY (charity_id) REFERENCES charity (id)');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF34EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF381C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649680CAB68 FOREIGN KEY (faculty_id) REFERENCES faculty (id)');
        $this->addSql('ALTER TABLE user_cache ADD CONSTRAINT FK_A38590BFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_cache ADD CONSTRAINT FK_A38590BF81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE user_cache ADD CONSTRAINT FK_A38590BF4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE faculty_cache DROP FOREIGN KEY FK_5AC38379680CAB68');
        $this->addSql('ALTER TABLE faculty_cache DROP FOREIGN KEY FK_5AC3837981C06096');
        $this->addSql('ALTER TABLE faculty_cache DROP FOREIGN KEY FK_5AC383794EC001D1');
        $this->addSql('ALTER TABLE faculty_extra_points DROP FOREIGN KEY FK_7708E4C32FA2CE37');
        $this->addSql('ALTER TABLE faculty_extra_points DROP FOREIGN KEY FK_7708E4C3A76ED395');
        $this->addSql('ALTER TABLE faculty_extra_points DROP FOREIGN KEY FK_7708E4C32D458B39');
        $this->addSql('ALTER TABLE profile_cache DROP FOREIGN KEY FK_B693F435A76ED395');
        $this->addSql('ALTER TABLE profile_cache DROP FOREIGN KEY FK_B693F43581C06096');
        $this->addSql('ALTER TABLE rejected_submission_message DROP FOREIGN KEY FK_2FA1DFE8BF396750');
        $this->addSql('ALTER TABLE season DROP FOREIGN KEY FK_F0E45BA9F5C97E37');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF34EC001D1');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF3A76ED395');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF381C06096');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649680CAB68');
        $this->addSql('ALTER TABLE user_cache DROP FOREIGN KEY FK_A38590BFA76ED395');
        $this->addSql('ALTER TABLE user_cache DROP FOREIGN KEY FK_A38590BF81C06096');
        $this->addSql('ALTER TABLE user_cache DROP FOREIGN KEY FK_A38590BF4EC001D1');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE charity');
        $this->addSql('DROP TABLE extra_points');
        $this->addSql('DROP TABLE faculty');
        $this->addSql('DROP TABLE faculty_cache');
        $this->addSql('DROP TABLE faculty_extra_points');
        $this->addSql('DROP TABLE profile_cache');
        $this->addSql('DROP TABLE rejected_submission_message');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE submission');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_cache');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
