<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fetchSubmission'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?bool $active = null;

    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?int $min_elevation = null;

    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: ProfileCache::class)]
    private Collection $profileCaches;

//    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: Submission::class, orphanRemoval: true)]
//    private Collection $submissions;

    public function __construct()
    {
//        $this->submissions = new ArrayCollection();
$this->profileCaches = new ArrayCollection();
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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getMinElevation(): ?int
    {
        return $this->min_elevation;
    }

    public function setMinElevation(int $min_elevation): self
    {
        $this->min_elevation = $min_elevation;

        return $this;
    }

    /**
     * @return Collection<int, ProfileCache>
     */
    public function getProfileCaches(): Collection
    {
        return $this->profileCaches;
    }

    public function addProfileCache(ProfileCache $profileCache): static
    {
        if (!$this->profileCaches->contains($profileCache)) {
            $this->profileCaches->add($profileCache);
            $profileCache->setActivity($this);
        }

        return $this;
    }

    public function removeProfileCache(ProfileCache $profileCache): static
    {
        if ($this->profileCaches->removeElement($profileCache)) {
            // set the owning side to null (unless already changed)
            if ($profileCache->getActivity() === $this) {
                $profileCache->setActivity(null);
            }
        }

        return $this;
    }
}
