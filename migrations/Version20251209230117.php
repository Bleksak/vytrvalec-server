<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251209230117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQLPlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL84Platform'."
        );

        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, active TINYINT NOT NULL, min_elevation INT NOT NULL, icon_uuid BINARY(16) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');

        $this->addSql('CREATE TABLE image (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', path VARCHAR(512) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, uploaded_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', used_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', original_mime_type VARCHAR(32) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX idx_used_at (used_at), PRIMARY KEY (uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');

        $this->addSql('CREATE TABLE faculty (id INT AUTO_INCREMENT NOT NULL, shortcut VARCHAR(10) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, visible TINYINT NOT NULL, parent_id INT DEFAULT NULL, color VARCHAR(9) CHARACTER SET utf8mb3 DEFAULT \'\' NOT NULL COLLATE `utf8mb3_general_ci`, INDEX IDX_17966043727ACA70 (parent_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE faculty ADD CONSTRAINT `fk_parent_id` FOREIGN KEY (parent_id) REFERENCES faculty (id) ON UPDATE NO ACTION ON DELETE NO ACTION');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, faculty_id INT NOT NULL, email VARCHAR(180) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`, roles JSON NOT NULL, password VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, banned TINYINT NOT NULL, first_name VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, last_name VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, token VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`, password_reset_token VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`, anonymize TINYINT DEFAULT NULL, mailing TINYINT DEFAULT 1 NOT NULL, email_unsubscribe_hash VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`, locale VARCHAR(8) CHARACTER SET utf8mb3 DEFAULT \'cs_CZ\' NOT NULL COLLATE `utf8mb3_general_ci`, INDEX email_unsubscribe_hash (email_unsubscribe_hash), INDEX IDX_8D93D649680CAB68 (faculty_id), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT `FK_8D93D649680CAB68` FOREIGN KEY (faculty_id) REFERENCES faculty (id) ON UPDATE NO ACTION ON DELETE NO ACTION');

        $this->addSql('CREATE TABLE activity_translation (locale VARCHAR(6) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, activity_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_BAE72F6381C06096 (activity_id), PRIMARY KEY (locale, activity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE activity_translation ADD CONSTRAINT `fk_activity_id` FOREIGN KEY (activity_id) REFERENCES activity (id) ON UPDATE NO ACTION ON DELETE NO ACTION');

        $this->addSql('CREATE TABLE charity (id INT AUTO_INCREMENT NOT NULL, image_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', website VARCHAR(512) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`, INDEX IDX_837DB71E2345BA38 (image_uuid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE charity ADD CONSTRAINT `FK_837DB71E2345BA38` FOREIGN KEY (image_uuid) REFERENCES image (uuid) ON UPDATE NO ACTION ON DELETE NO ACTION');

        $this->addSql('CREATE TABLE charity_translation (locale VARCHAR(6) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, charity_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(10000) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_252290D1F5C97E37 (charity_id), PRIMARY KEY (locale, charity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE charity_translation ADD CONSTRAINT `fk_charity_id` FOREIGN KEY (charity_id) REFERENCES charity (id) ON UPDATE NO ACTION ON DELETE NO ACTION');

        $this->addSql('CREATE TABLE extra_points (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, points INT NOT NULL, week INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');

        $this->addSql('CREATE TABLE faculty_translation (locale VARCHAR(6) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, faculty_id INT NOT NULL, INDEX IDX_F9E9D3BF680CAB68 (faculty_id), PRIMARY KEY (locale, faculty_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE faculty_translation ADD CONSTRAINT `fk_faculty_id` FOREIGN KEY (faculty_id) REFERENCES faculty (id) ON UPDATE NO ACTION ON DELETE NO ACTION');

        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, headers LONGTEXT CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, queue_name VARCHAR(190) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E016BA31DB (delivered_at), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E0FB7336F0 (queue_name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');

        $this->addSql('CREATE TABLE profile_cache (user_id INT NOT NULL, activity_id INT NOT NULL, distance INT NOT NULL, elevation INT NOT NULL, INDEX IDX_B693F43581C06096 (activity_id), INDEX IDX_B693F435A76ED395 (user_id), PRIMARY KEY (user_id, activity_id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE profile_cache ADD CONSTRAINT `FK_B693F43581C06096` FOREIGN KEY (activity_id) REFERENCES activity (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE profile_cache ADD CONSTRAINT `FK_B693F435A76ED395` FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');

        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, charity_id INT NOT NULL, start DATE NOT NULL, end DATE NOT NULL, INDEX IDX_F0E45BA9F5C97E37 (charity_id), INDEX date_index (start), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT `FK_F0E45BA9F5C97E37` FOREIGN KEY (charity_id) REFERENCES charity (id) ON UPDATE NO ACTION ON DELETE NO ACTION');

        $this->addSql('CREATE TABLE submission (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, user_id INT NOT NULL, activity_id INT NOT NULL, accepted TINYINT NOT NULL, elevation BIGINT NOT NULL, distance BIGINT NOT NULL, reviewed TINYINT NOT NULL, week INT NOT NULL, date DATE NOT NULL, updated_at DATETIME NOT NULL, message VARCHAR(512) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, image_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_DB055AF32345BA38 (image_uuid), INDEX IDX_DB055AF34EC001D1 (season_id), INDEX IDX_DB055AF381C06096 (activity_id), INDEX IDX_DB055AF3A76ED395 (user_id), INDEX week_index (week), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT `FK_DB055AF34EC001D1` FOREIGN KEY (season_id) REFERENCES season (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT `FK_DB055AF381C06096` FOREIGN KEY (activity_id) REFERENCES activity (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT `FK_DB055AF3A76ED395` FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT `fk_image_uuid` FOREIGN KEY (image_uuid) REFERENCES image (uuid) ON UPDATE NO ACTION ON DELETE NO ACTION');

        $this->addSql('CREATE TABLE cache (season_id INT NOT NULL, created_at DATETIME NOT NULL, data JSON NOT NULL, PRIMARY KEY (season_id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cache ADD CONSTRAINT `FK_41476BE74EC001D1` FOREIGN KEY (season_id) REFERENCES season (id) ON UPDATE NO ACTION ON DELETE NO ACTION');

        $this->addSql('CREATE TABLE faculty_mapping (season_id INT NOT NULL, faculty_id INT NOT NULL, parent_id INT DEFAULT NULL, INDEX IDX_8F5FAD01E84A13 (parent_id), INDEX IDX_8F5FAD0A00563A6 (season_id), INDEX IDX_8F5FAD0A03B794C (faculty_id), PRIMARY KEY (season_id, faculty_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE faculty_mapping ADD CONSTRAINT `fk_faculty` FOREIGN KEY (faculty_id) REFERENCES faculty (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE faculty_mapping ADD CONSTRAINT `fk_parent` FOREIGN KEY (parent_id) REFERENCES faculty (id) ON UPDATE NO ACTION ON DELETE NO ACTION');

        $this->addSql('ALTER TABLE faculty_mapping ADD CONSTRAINT `fk_season` FOREIGN KEY (season_id) REFERENCES season (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQLPlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL84Platform'."
        );


        $this->addSql('DROP TABLE `cache`');

        $this->addSql('DROP TABLE `extra_points`');

        $this->addSql('DROP TABLE `messenger_messages`');

        $this->addSql('DROP TABLE `profile_cache`');

        $this->addSql('DROP TABLE `submission`');

        $this->addSql('DROP TABLE `faculty_mapping`');

        $this->addSql('DROP TABLE `season`');

        $this->addSql('DROP TABLE `charity_translation`');
        $this->addSql('DROP TABLE `charity`');

        $this->addSql('DROP TABLE `user`');

        $this->addSql('DROP TABLE `faculty_translation`');
        $this->addSql('DROP TABLE `faculty`');

        $this->addSql('DROP TABLE `image`');

        $this->addSql('DROP TABLE `activity_translation`');

        $this->addSql('DROP TABLE `activity`');
    }
}
