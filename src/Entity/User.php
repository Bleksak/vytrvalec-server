<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\AnonymizedUser;
use App\Dto\User\Response\UserResponseDto;
use App\Repository\UserRepository;
use App\Utils\FeatureFlag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Index(
    columns: ['email_unsubscribe_hash'],
    name: 'email_unsubscribe_hash',
)]
final class User extends AbstractEntity implements
    UserInterface,
    PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\Column(length: 180, unique: true, nullable: true)]
    public ?string $email = null;

    /**
     * @var array<string>
     */
    #[ORM\Column(type: 'json')]
    public array $roles = [];

    #[ORM\Column]
    public string $password;

    #[ORM\Column]
    public bool $banned = false;

    #[ORM\Column(options: ['default' => 1])]
    public bool $mailing = true;

    #[ORM\Column(type: 'string', length: 255)]
    public string $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    public string $lastName;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    public Faculty $faculty;

    /**
     * @var Collection<int, Submission>
     */
    #[ORM\OneToMany(
        mappedBy: 'user',
        targetEntity: Submission::class,
        orphanRemoval: true,
    )]
    public Collection $submissions;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $token = null;

    /**
     * @var Collection<int, ProfileCache>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ProfileCache::class)]
    public Collection $profileCaches;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $passwordResetToken = null;

    #[ORM\Column(nullable: true)]
    public ?bool $anonymize = false;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $emailUnsubscribeHash = null;

    #[ORM\Column(length: 8)]
    public string $locale = 'cs_CZ';

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
        #[SensitiveParameter]
        ?string $token = null,
        string $locale = 'cs_CZ',
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
        $this->emailUnsubscribeHash = \bin2hex(\random_bytes(90));
        $this->locale = $locale;
    }

    #[\Override]
    public function getUserIdentifier(): string
    {
        if ($this->email === null || $this->email === '') {
            return 'null';
        }

        return $this->email;
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

        return \array_unique($roles);
    }

    #[Ignore]
    public function hasRole(string $roleName): bool
    {
        return \in_array($roleName, $this->getRoles(), true);
    }

    #[\Override]
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(#[SensitiveParameter] string $password): self
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

    public function setToken(#[SensitiveParameter] ?string $token): self
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

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(
        #[SensitiveParameter]
        ?string $passwordResetToken,
    ): self {
        $this->passwordResetToken = $passwordResetToken;

        return $this;
    }

    public function getEmailUnsubscribeHash(): string
    {
        if ($this->emailUnsubscribeHash === null) {
            $this->emailUnsubscribeHash = \bin2hex(\random_bytes(90));
        }

        return $this->emailUnsubscribeHash;
    }

    public function resetEmailUnsubscribeHash(): void
    {
        $this->emailUnsubscribeHash = null;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function anonymize(): self
    {
        $this->resetEmailUnsubscribeHash();
        $this->anonymize = true;

        $this
            ->setLastName('')
            ->setMailing(false)
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
            $this->id,
            $this->email,
            $this->roles,
            $this->banned,
            $this->mailing,
            $this->firstName,
            $this->lastName,
            $this->faculty->toResponseObject(),
            $this->anonymize,
        );
    }

    public function toAnonymizedUser(): AnonymizedUser
    {
        return new AnonymizedUser(
            $this->firstName,
            $this->lastName,
            $this->anonymize,
        );
    }
}
