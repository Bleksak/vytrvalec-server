<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204225217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add required indexes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE INDEX idx_submission_accepted_season_user ON submission (accepted, season_id, user_id);',
        );
        $this->addSql(
            'CREATE INDEX idx_submission_accepted_activity ON submission (accepted, activity_id);',
        );
        $this->addSql(
            'CREATE INDEX idx_activity_icon_uuid ON activity (icon_uuid);',
        );
        $this->addSql(
            'CREATE INDEX idx_submission_accepted_activity_distance ON submission (accepted, activity_id, distance);',
        );
        $this->addSql(
            'ALTER TABLE activity_translation ADD UNIQUE KEY uniq_activity_locale (activity_id, locale);',
        );
        $this->addSql(
            'CREATE INDEX idx_submission_accepted_season_activity_user_distance ON submission (accepted, season_id, activity_id, user_id, distance);',
        );
        $this->addSql(
            'CREATE INDEX idx_submission_season_week_accepted ON submission (season_id, week, accepted);',
        );
        $this->addSql(
            'CREATE INDEX idx_season_start_end_charity_id ON season(start, end, charity_id);',
        );

        $this->addSql(
            'ALTER TABLE `charity_translation`
            DROP INDEX `PRIMARY`,
            ADD PRIMARY KEY `charity_id_locale` (`charity_id`, `locale`);',
        );
        $this->addSql(
            'ALTER TABLE `faculty_translation`
            DROP INDEX `PRIMARY`,
            ADD PRIMARY KEY `PRIMARY` (`faculty_id`, `locale`);',
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'DROP INDEX idx_submission_accepted_season_user ON submission;',
        );
        $this->addSql(
            'DROP INDEX idx_submission_accepted_activity ON submission;',
        );
        $this->addSql('DROP INDEX idx_activity_icon_uuid ON activity;');
        $this->addSql(
            'DROP INDEX idx_submission_accepted_activity_distance ON submission;',
        );
        $this->addSql(
            'ALTER TABLE activity_translation DROP INDEX uniq_activity_locale;',
        );
        $this->addSql(
            'DROP INDEX idx_submission_accepted_season_activity_user_distance ON submission;',
        );
        $this->addSql(
            'DROP INDEX idx_submission_season_week_accepted ON submission;',
        );
        $this->addSql('DROP INDEX idx_season_start_end_charity_id ON season;');

        $this->addSql('
            ALTER TABLE `charity_translation`
            DROP PRIMARY KEY,
            ADD PRIMARY KEY (`locale`, `charity_id`);
        ');

        $this->addSql(
            'ALTER TABLE `faculty_translation`
            DROP PRIMARY KEY,
            ADD PRIMARY KEY `PRIMARY` (`locale`, `faculty_id`);',
        );
    }
}
