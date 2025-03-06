<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission', 'fetchUser'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['fetchSubmission', 'fetchUser'])]
    private ?string $email = null;

    /**
     * @var array<string>
     */
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string'))]
    #[ORM\Column(type: 'json')]
    #[Groups(['fetchSubmission', 'fetchUser'])]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    #[Groups(['fetchSubmission', 'fetchUser'])]
    private ?bool $banned = false;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['fetchSubmission', 'fetchUser'])]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['fetchSubmission', 'fetchUser'])]
    private ?string $lastName = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'], fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSubmission', 'fetchUser'])]
    private ?Faculty $faculty = null;

    /**
     * @var Collection<int, Submission>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Submission::class, orphanRemoval: true)]
    private ?Collection $submissions;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    /**
     * @var Collection<int, ProfileCache>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ProfileCache::class)]
    private Collection $profileCaches;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $passwordResetToken = null;

    #[ORM\Column]
    private ?bool $acceptedGdpr = false;

    /**
     * @param array<string> $roles
     */
    public function __construct(
        string $email,
        string $firstName,
        string $lastName,
        Faculty $faculty,
        bool $acceptedGdpr,
        array $roles = [],
        ?string $token = null,
    ) {
        $this->submissions = new ArrayCollection();
        $this->profileCaches = new ArrayCollection();

        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->faculty = $faculty;
        $this->acceptedGdpr = $acceptedGdpr;
        $this->roles = $roles;
        $this->token = $token;
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

    public function setAcceptedGdpr(bool $value): self
    {
        $this->acceptedGdpr = $value;

        return $this;
    }

    public function hasAcceptedGdpr(): ?bool
    {
        return $this->acceptedGdpr;
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
     * @return array<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    #[Ignore]
    public function hasRole(string $roleName): bool
    {
        return in_array($roleName, $this->getRoles(), true);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Collection<int, ProfileCache>
     */
    public function getProfileCaches(): Collection
    {
        return $this->profileCaches;
    }

    public function eraseCredentials(): void
    {
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $passwordResetToken): static
    {
        $this->passwordResetToken = $passwordResetToken;

        return $this;
    }
}
