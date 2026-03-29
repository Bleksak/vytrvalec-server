<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ImageRepository;
use App\Services\ImagePath;
use App\Utils\MimeType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Index(columns: ['used_at'], name: 'idx_used_at')]
#[ORM\Entity(repositoryClass: ImageRepository::class)]
final class Image extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    public private(set) Uuid $uuid;

    #[ORM\Column(length: 512)]
    public private(set) string $path;

    #[ORM\Column]
    public \DateTime $uploadedAt;

    #[ORM\Column(nullable: true)]
    public ?\DateTime $usedAt = null;

    #[ORM\Column(enumType: MimeType::class, length: 32)]
    public MimeType $originalMimeType;

    public function __construct(string $path, MimeType $originalMimeType)
    {
        $this->path = $path;
        $this->originalMimeType = $originalMimeType;
        $this->uuid = Uuid::v7();
        $this->uploadedAt = new \DateTime();
        $this->usedAt = null;
    }

    public function getPath(?ImagePath $imagePath = null): string
    {
        if ($imagePath === null) {
            return $this->path;
        }

        return $imagePath->fullPath($this->path);
    }
}
