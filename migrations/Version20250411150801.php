<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Image;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Bridge\Doctrine\Types\UuidType;

final class Version20250411150801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image_uuid to submission and populate image with images from submissions';
    }

    public function up(Schema $schema): void
    {
        $connection = $this->connection;

        $oldImages = $connection->executeQuery('select image from submission where image != \'\'')->fetchAllAssociative();

        $connection->beginTransaction();

        foreach ($oldImages as $oldImage) {
            $oldImage = $oldImage['image'];

            $image = (new Image($oldImage))->setUsedAt(new \DateTimeImmutable());

            $connection->executeQuery(
                'INSERT INTO image(uuid, path, uploaded_at, used_at) VALUES(?, ?, ?, ?)',
                [
                    $image->getUuid()->toBinary(),
                    $image->getPath(),
                    $image->getUploadedAt()->format('Y-m-d h:i:s'),
                    $image->getUsedAt()->format('Y-m-d h:i:s'),
                ]
            );
        }

        $connection->commit();

        $table = $schema->getTable('submission');

        $table
            ->addColumn('image_uuid', UuidType::NAME)
            ->setNotnull(false)
            ->setDefault(null);

        $table->addForeignKeyConstraint('image', ['image_uuid'], ['uuid'], name: 'fk_image_uuid');
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('submission');
        $table->removeForeignKey('fk_image_uuid');
        $table->dropColumn('image_uuid');

        $this->addSql('set foreign_key_checks = 0');
        $this->addSql('truncate table image');
        $this->addSql('set foreign_key_checks = 1');
    }
}
