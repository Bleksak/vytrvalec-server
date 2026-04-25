<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260425113540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create sponsor and season_sponsor tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if($this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SQLitePlatform) {
            $this->addSql('CREATE TABLE sponsor (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, image_id BLOB DEFAULT NULL, CONSTRAINT FK_818CC9D43DA5256D FOREIGN KEY (image_id) REFERENCES image (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        	$this->addSql('CREATE INDEX IDX_818CC9D43DA5256D ON sponsor (image_id)');
        	$this->addSql('CREATE TABLE season_sponsor (season_id INTEGER NOT NULL, sponsor_id INTEGER NOT NULL, PRIMARY KEY (season_id, sponsor_id), CONSTRAINT FK_A4F5608A4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A4F5608A12F7FB51 FOREIGN KEY (sponsor_id) REFERENCES sponsor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        	$this->addSql('CREATE INDEX IDX_A4F5608A4EC001D1 ON season_sponsor (season_id)');
        	$this->addSql('CREATE INDEX IDX_A4F5608A12F7FB51 ON season_sponsor (sponsor_id)');
        }
        if($this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQLPlatform) {
            $this->addSql('CREATE TABLE sponsor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, image_id BINARY(16) DEFAULT NULL, INDEX IDX_818CC9D43DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        	$this->addSql('CREATE TABLE season_sponsor (season_id INT NOT NULL, sponsor_id INT NOT NULL, INDEX IDX_A4F5608A4EC001D1 (season_id), INDEX IDX_A4F5608A12F7FB51 (sponsor_id), PRIMARY KEY (season_id, sponsor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        	$this->addSql('ALTER TABLE sponsor ADD CONSTRAINT FK_818CC9D43DA5256D FOREIGN KEY (image_id) REFERENCES image (uuid)');
        	$this->addSql('ALTER TABLE season_sponsor ADD CONSTRAINT FK_A4F5608A4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id) ON DELETE CASCADE');
        	$this->addSql('ALTER TABLE season_sponsor ADD CONSTRAINT FK_A4F5608A12F7FB51 FOREIGN KEY (sponsor_id) REFERENCES sponsor (id) ON DELETE CASCADE');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        if($this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SQLitePlatform) {
        	$this->addSql('DROP TABLE sponsor');
        	$this->addSql('DROP TABLE season_sponsor');
        }
        if($this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQLPlatform) {
        	$this->addSql('ALTER TABLE sponsor DROP FOREIGN KEY FK_818CC9D43DA5256D');
        	$this->addSql('ALTER TABLE season_sponsor DROP FOREIGN KEY FK_A4F5608A4EC001D1');
        	$this->addSql('ALTER TABLE season_sponsor DROP FOREIGN KEY FK_A4F5608A12F7FB51');
        	$this->addSql('DROP TABLE sponsor');
        	$this->addSql('DROP TABLE season_sponsor');
        }
    }
}
