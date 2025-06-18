<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ImageRepository;
use App\Services\ImagePath;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Index(columns: ['used_at'], name: 'idx_used_at')]
#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $uuid;

    #[ORM\Column(length: 512)]
    private string $path;

    #[ORM\Column]
    private \DateTimeImmutable $uploadedAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $usedAt = null;

    public function __construct(
        string $path,
    ) {
        $this->path = $path;
        $this->uuid = Uuid::v7();
        $this->uploadedAt = new \DateTimeImmutable();
        $this->usedAt = null;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getPath(?ImagePath $imagePath = null): string
    {
        if ($imagePath === null) {
            return $this->path;
        }

        return $imagePath->fullPath($this->path);
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getUploadedAt(): \DateTimeImmutable
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(\DateTimeImmutable $uploadedAt): static
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    public function getUsedAt(): ?\DateTimeImmutable
    {
        return $this->usedAt;
    }

    public function setUsedAt(?\DateTimeImmutable $usedAt): static
    {
        $this->usedAt = $usedAt;

        return $this;
    }
}
