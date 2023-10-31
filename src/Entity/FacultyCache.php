<?php

namespace App\Entity;

use App\Repository\FacultyCacheRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FacultyCacheRepository::class)]
class FacultyCache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSeasonResult'])]
    private ?int $id = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSeasonResult'])]
    private ?Faculty $faculty = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSeasonResult'])]
    private ?Activity $activity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSeasonResult'])]
    private ?Season $season = null;

    #[ORM\Column]
    #[Groups(['fetchSeasonResult'])]
    private ?int $week = null;

    #[ORM\Column]
    #[Groups(['fetchSeasonResult'])]
    private ?int $distance = null;

    #[ORM\Column]
    #[Groups(['fetchSeasonResult'])]
    private ?int $elevation = null;

    #[ORM\OneToMany(mappedBy: 'facultyCache', targetEntity: FacultyExtraPoints::class, fetch: 'EAGER')]
    #[Groups(['fetchSeasonResult'])]
    private Collection $extraPoints;

    public function __construct(Faculty $faculty, Activity $activity, Season $season, int $week)
    {
        $this->extraPoints = new ArrayCollection();
        $this->faculty = $faculty;
        $this->activity = $activity;
        $this->season = $season;
        $this->week = $week;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    public function setSeason(?Season $season): static
    {
        $this->season = $season;

        return $this;
    }

    public function getWeek(): ?int
    {
        return $this->week;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): static
    {
        $this->activity = $activity;

        return $this;
    }

    public function setWeek(int $week): static
    {
        $this->week = $week;

        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(int $distance): static
    {
        $this->distance = $distance;

        return $this;
    }

    public function getElevation(): ?int
    {
        return $this->elevation;
    }

    public function setElevation(int $elevation): static
    {
        $this->elevation = $elevation;

        return $this;
    }

    public function getFaculty(): ?Faculty
    {
        return $this->faculty;
    }

    public function setFaculty(?Faculty $faculty): static
    {
        $this->faculty = $faculty;

        return $this;
    }

    /**
     * @return Collection<int, FacultyExtraPoints>
     */
    public function getExtraPoints(): Collection
    {
        return $this->extraPoints;
    }

    public function updateDistance(callable $updateFn): static
    {
        $this->setDistance(call_user_func($updateFn, $this->getDistance()));
        return $this;
    }

    public function updateElevation(callable $updateFn): static
    {
        $this->setElevation(call_user_func($updateFn, $this->getElevation()));
        return $this;
    }
}
