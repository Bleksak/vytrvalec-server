<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250411152112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Populate image uuid in submission';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX temp_idx_submission_image ON submission(image)');
        $this->addSql('CREATE INDEX temp_idx_image_path ON image(path)');

        $this->addSql('
            update submission s
            join image i on s.image = i.path
            set s.image_uuid = i.uuid
            where s.image != \'\'
        ');

        $this->addSql('DROP INDEX temp_idx_submission_image ON submission');
        $this->addSql('DROP INDEX temp_idx_image_path ON image');

        $schema
            ->getTable('submission')
            ->getColumn('image')
            ->setNotnull(false);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('update submission set image_uuid = null where 1=1');
    }
}
