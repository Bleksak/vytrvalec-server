<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\User\Response\UserResponseDto;
use App\Repository\UserRepository;
use App\Utils\FeatureFlag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Index(columns: ['email_unsubscribe_hash'], name: 'email_unsubscribe_hash')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true, nullable: true)]
    #[Groups(['fetchSubmission'])]
    private ?string $email = null;

    /**
     * @var array<string>
     */
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string'))]
    #[ORM\Column(type: 'json')]
    #[Groups(['fetchSubmission'])]
    private array $roles = [];

    #[ORM\Column]
    private string $password;

    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private bool $banned = false;

    #[ORM\Column(options: ['default' => 1])]
    private bool $mailing = true;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['fetchSubmission'])]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['fetchSubmission'])]
    private string $lastName;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'], fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSubmission'])]
    private Faculty $faculty;

    /**
     * @var Collection<int, Submission>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Submission::class, orphanRemoval: true)]
    private Collection $submissions;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    /**
     * @var Collection<int, ProfileCache>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ProfileCache::class)]
    private Collection $profileCaches;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $passwordResetToken = null;

    #[ORM\Column(nullable: true)]
    private ?bool $anonymize = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailUnsubscribeHash = null;

    /**
     * @param array<string> $roles
     */
    public function __construct(
        string $email,
        string $firstName,
        string $lastName,
        Faculty $faculty,
        bool $anonymize,
        array $roles = [],
        ?string $token = null,
    ) {
        $this->submissions = new ArrayCollection();
        $this->profileCaches = new ArrayCollection();

        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->faculty = $faculty;
        $this->anonymize = $anonymize;
        $this->roles = $roles;
        $this->token = $token;
        $this->emailUnsubscribeHash = bin2hex(random_bytes(90));
    }

    public function getId(): int
    {
        return $this->id ?? 0;
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

    public function setAnonymization(bool $value): self
    {
        $this->anonymize = $value;

        return $this;
    }

    public function shouldAnonymize(): ?bool
    {
        return $this->anonymize;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    #[\Override]
    public function getUserIdentifier(): string
    {
        // @phpstan-ignore-next-line
        return $this->email ?? 'null';
    }

    /**
     * @return array<string>
     */
    #[\Override]
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

    #[\Override]
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFaculty(): Faculty
    {
        return $this->faculty;
    }

    public function setFaculty(Faculty $faculty): self
    {
        $this->faculty = $faculty;

        return $this;
    }

    public function isBanned(): bool
    {
        return $this->banned;
    }

    public function setBanned(bool $banned): self
    {
        $this->banned = $banned;

        return $this;
    }

    public function hasMailing(): bool
    {
        return $this->mailing;
    }

    public function setMailing(bool $mailing): self
    {
        $this->mailing = $mailing;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
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

    #[\Override]
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

    public function getEmailUnsubscribeHash(): ?string
    {
        return $this->emailUnsubscribeHash;
    }

    public function setEmailUnsubscribeHash(?string $emailUnsubscribeHash): static
    {
        $this->emailUnsubscribeHash = $emailUnsubscribeHash;

        return $this;
    }

    public function anonymize(): static
    {
        $this
            ->setLastName('')
            ->setMailing(false)
            ->setAnonymization(true)
            ->setEmailUnsubscribeHash(null)
            ->setToken(null)
            ->setPasswordResetToken(null);

        $this->email = null;

        return $this;
    }

    public function canAccess(FeatureFlag $featureFlag): bool
    {
        return \in_array($featureFlag->value, $this->roles, true);
    }

    public function toResponseObject(): UserResponseDto
    {
        return new UserResponseDto(
            $this->getId(),
            $this->getEmail(),
            $this->getRoles(),
            $this->isBanned(),
            $this->hasMailing(),
            $this->getFirstName(),
            $this->getLastName(),
            $this->getFaculty()->toResponseObject(),
            $this->shouldAnonymize(),
        );
    }
}
