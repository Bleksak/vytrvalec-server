<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CharityRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CharityRepository::class)]
class Charity implements Translatable
{
    #[OA\Property]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?int $id = null;

    #[OA\Property]
    #[ORM\Column(length: 255)]
    #[Groups(['fetchSubmission'])]
    private string $name;

    #[OA\Property]
    #[ORM\Column(length: 10000)]
    #[Gedmo\Translatable]
    #[Groups(['fetchSubmission'])]
    private string $description;

    #[OA\Property]
    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: true, referencedColumnName: 'uuid', name: 'image_uuid')]
    #[Groups(['fetchSubmission'])]
    private ?Image $image = null;

    #[OA\Property]
    #[ORM\Column(length: 512, nullable: true)]
    #[Groups(['fetchSubmission'])]
    private ?string $website = null;

    public function __construct(
        string $name,
        string $description,
        ?Image $image = null,
        ?string $website = null,
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;
        $this->website = $website;
    }

    public function getId(): int
    {
        return $this->id ?? 0;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }
}
