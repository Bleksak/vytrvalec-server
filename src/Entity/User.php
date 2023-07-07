<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\UserRepository;
use App\State\UserPasswordHasher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    uriTemplate: '/user/',
    operations: [
        new Get(
            uriTemplate: '/user/{id}',
            security: 'is_granted(\'ROLE_STAFF\') or object == user'
        ),
        new Post(
            uriTemplate: '/user/register',
            denormalizationContext: ['groups' => ['user:create']],
            security: 'not is_granted(\'ROLE_USER\')',
            validationContext: ['groups' => ['Default', 'user:create']],
            output: false,
            read: false,
            processor: UserPasswordHasher::class
        ),
        new GetCollection(
            uriTemplate: '/user/list',
            security: 'is_granted(\'ROLE_STAFF\')',
        ),
        new Patch(
            uriTemplate: '/user/update',
            denormalizationContext: ['groups' => ['user:update']],
            processor: UserPasswordHasher::class
        ),
        new Patch(
            uriTemplate: '/user/update/{id}',
            denormalizationContext: ['groups' => ['user:adminUpdate']],
            processor: UserPasswordHasher::class
        ),
        new Delete(),
        new Post(
            routeName: 'api_user_login'
        ),
        new Get(
            routeName: 'api_user_logout'
        )
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:create', 'user:update']],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    private ?string $email = null;

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'json')]
    #[Groups(['user:read', 'user:adminUpdate'])]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(groups: ['user:create'])]
    #[Groups(['user:create', 'user:update'])]
    private ?string $plainPassword = null;

    #[ORM\Column]
    #[Groups(['user:read', 'user:adminUpdate'])]
    private ?bool $banned = false;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['user:read', 'user:create'])]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['user:read', 'user:create'])]
    private ?string $lastName = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:read', 'user:create'])]
    private ?Faculty $faculty = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Submission::class)]
    #[Groups(['user:read'])]
    private Collection $submissions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserSummary::class)]
    #[Groups(['user:read'])]
    private Collection $userSummaries;

    public function __construct()
    {
        $this->submissions = new ArrayCollection();
        $this->userSummaries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function hasRole(string $roleName): bool
    {
        return in_array($roleName, $this->getRoles(), true);
    }

    /**
     * @param string[] $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFaculty(): ?Faculty
    {
        return $this->faculty;
    }

    public function setFaculty(Faculty $faculty): self
    {
        $this->faculty = $faculty;

        return $this;
    }

    public function isBanned(): ?bool
    {
        return $this->banned;
    }

    public function setBanned(bool $banned): self
    {
        $this->banned = $banned;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, Submission>
     */
    public function getSubmissions(): Collection
    {
        return $this->submissions;
    }

    public function addSubmission(Submission $submission): self
    {
        if (!$this->submissions->contains($submission)) {
            $this->submissions->add($submission);
            $submission->setUser($this);
        }

        return $this;
    }

    public function removeSubmission(Submission $submission): self
    {
        if ($this->submissions->removeElement($submission)) {
            // set the owning side to null (unless already changed)
            if ($submission->getUser() === $this) {
                $submission->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserSummary>
     */
    public function getUserSummaries(): Collection
    {
        return $this->userSummaries;
    }

    public function addUserSummary(UserSummary $userSummary): self
    {
        if (!$this->userSummaries->contains($userSummary)) {
            $this->userSummaries->add($userSummary);
            $userSummary->setUser($this);
        }

        return $this;
    }

    public function removeUserSummary(UserSummary $userSummary): self
    {
        if ($this->userSummaries->removeElement($userSummary)) {
            // set the owning side to null (unless already changed)
            if ($userSummary->getUser() === $this) {
                $userSummary->setUser(null);
            }
        }

        return $this;
    }
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }
}
