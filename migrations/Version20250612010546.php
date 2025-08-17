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
