<?php

namespace App\Entity;

use App\Repository\FacultyExtraPointsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FacultyExtraPointsRepository::class)]
class FacultyExtraPoints
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'extraPoints')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FacultyCache $facultyCache = null;

    #[ORM\Id]
    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'extraPoints')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSeasonResult'])]
    private ?User $user = null;

    #[ORM\Id]
    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSeasonResult'])]
    private ?ExtraPoints $extraPoints = null;

    #[ORM\Column]
    private ?int $value = null;

    public function __construct(FacultyCache $facultyCache, User $user, ExtraPoints $extraPoints, int $value)
    {
        $this->facultyCache = $facultyCache;
        $this->user = $user;
        $this->extraPoints = $extraPoints;
        $this->value = $value;
    }
}
