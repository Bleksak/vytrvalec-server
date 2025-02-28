<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250228104937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop rejected messages, add message to Submission and move all data from rejected to it';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            alter table submission
                add message varchar(512) not null default ""
        ');

        $this->addSql('
            update submission s set message = coalesce((
                select message from rejected_submission_message sm
                where sm.id = s.id
            ), "")
        ');

        $this->addSql('drop table rejected_submission_message');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            create table rejected_submission_message(
                id BIGINT PRIMARY KEY NOT NULL,
                message VARCHAR(512) NOT NULL default \'\',
                created_at DATETIME NOT NULL default CURRENT_TIMESTAMP
            )
        ');

        $this->addSql('
            insert into rejected_submission_message(id, message, created_at)
            select s.id, s.message, s.updated_at from submission s
            where s.reviewed = 1 and s.accepted = 0 and s.message != \'\'
        ');

        $this->addSql('
            alter table submission
                drop column message
        ');
    }
}
