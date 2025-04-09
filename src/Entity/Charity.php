<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CharityRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CharityRepository::class)]
class Charity
{
    #[OA\Property]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission', 'fetchSeasonList', 'fetchSeasonResult', 'fetchCharity'])]
    private ?int $id = null;

    #[OA\Property]
    #[ORM\Column(length: 255)]
    #[Groups(['fetchSubmission', 'fetchSeasonList', 'fetchSeasonResult', 'fetchCharity'])]
    private ?string $name = null;

    #[OA\Property]
    #[ORM\Column(length: 10000)]
    #[Groups(['fetchSubmission', 'fetchSeasonList', 'fetchSeasonResult', 'fetchCharity'])]
    private ?string $description = null;

    #[OA\Property]
    #[ORM\Column(length: 512)]
    #[Groups(['fetchSubmission', 'fetchSeasonList', 'fetchSeasonResult', 'fetchCharity'])]
    private ?string $image = null;

    #[OA\Property]
    #[ORM\Column(length: 512)]
    #[Groups(['fetchSubmission', 'fetchSeasonList', 'fetchSeasonResult', 'fetchCharity'])]
    private ?string $website = null;

    public function __construct(
        string $name,
        string $description,
        ?string $image = null,
        ?string $website = null,
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;
        $this->website = $website;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(string $website): self
    {
        $this->website = $website;

        return $this;
    }
}
