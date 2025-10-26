<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250508235759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename gdpr -> anonymize';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user CHANGE accepted_gdpr anonymize TINYINT(1) NULL;');

        $this->addSql(
            '
            UPDATE `user` SET `anonymize` = CASE
                WHEN `anonymize` IS NULL THEN NULL
                WHEN `anonymize` = 1 THEN 0
                ELSE 1
            END;
            '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            '
            UPDATE `user` SET `anonymize` = CASE
                WHEN `anonymize` IS NULL THEN NULL
                WHEN `anonymize` = 1 THEN 0
                ELSE 1
            END;
            '
        );

        $this->addSql('ALTER TABLE user CHANGE anonymize accepted_gdpr TINYINT(1) NULL;');
    }
}
