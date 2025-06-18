<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250612010546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(191) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', username VARCHAR(191) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(191) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), INDEX general_translations_lookup_idx (object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cache CHANGE created_at created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE data data JSON NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE submission CHANGE date date DATE NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE message message VARCHAR(512) NOT NULL
        SQL);

        $this->addSql('ALTER TABLE cache CHANGE created_at created_at DATETIME NOT NULL, CHANGE data data JSON NOT NULL');
        $this->addSql('alter table faculty drop foreign key parent_id');
        $this->addSql('alter table faculty add constraint fk_parent_id FOREIGN KEY (parent_id) REFERENCES faculty(id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('alter table faculty drop foreign key fk_parent_id');
        $this->addSql('alter table faculty add constraint parent_id FOREIGN KEY (parent_id) REFERENCES faculty(id)');

        $this->addSql(<<<'SQL'
            DROP TABLE ext_log_entries
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE ext_translations
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE faculty DROP FOREIGN KEY FK_17966043727ACA70
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE faculty ADD CONSTRAINT parent_id FOREIGN KEY (parent_id) REFERENCES faculty (id) ON UPDATE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE submission CHANGE date date DATE NOT NULL COMMENT '(DC2Type:date_immutable)', CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE message message VARCHAR(512) DEFAULT '' NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cache CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE data data JSON NOT NULL COMMENT '(DC2Type:json)'
        SQL);
    }
}
