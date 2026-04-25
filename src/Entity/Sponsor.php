<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SponsorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SponsorRepository::class)]
final class Sponsor extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\Column(length: 255)]
    public string $name;

    #[ORM\Column(length: 255)]
    public string $url;

    #[ORM\JoinColumn(referencedColumnName: 'uuid')]
    #[ORM\ManyToOne]
    public Image $image;

    /** @var Collection<int, Season> */
    #[ORM\ManyToMany(targetEntity: Season::class, mappedBy: 'sponsors')]
    public Collection $seasons;

    public function __construct(string $name, string $url, Image $image)
    {
        $this->name = $name;
        $this->url = $url;
        $this->image = $image;
        $this->seasons = new ArrayCollection();
    }
}
