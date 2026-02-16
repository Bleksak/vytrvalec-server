<?php

declare(strict_types=1);

namespace App\Action;

use App\Dto\EmailingChangeDto;
use App\Dto\PasswordChangeDto;
use App\Dto\User\ForgottenPasswordResetDto;
use App\Dto\User\PasswordResetDto;
use App\Dto\User\UserEditDto;
use App\Dto\User\UserLoginDto;
use App\Dto\UserRegistrationDto;
use App\Entity\User;
use App\Exceptions\User\InvalidFacultySelectedException;
use App\Exceptions\User\NonUniqueEmailException;
use App\Exceptions\User\PasswordInvalidException;
use App\Exceptions\User\UserNotFoundException;
use App\Notifications\EmailTemplate\ForgottenPasswordEmailTemplate;
use App\Notifications\EmailTemplate\RegisterEmailTemplate;
use App\Repository\FacultyRepository;
use App\Repository\UserRepository;
use App\Services\ClientUrlBuilderFactory;
use App\Services\VytrvalecMailer;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserActions
{
    public function __construct(
        private UserRepository $userRepository,
        private FacultyRepository $facultyRepository,
        private UserPasswordHasherInterface $hasher,
        private VytrvalecMailer $mailer,
        private ClientUrlBuilderFactory $clientUrlBuilderFactory,
    ) {}

    /**
     * @throws NonUniqueEmailException
     * @throws InvalidFacultySelectedException
     */
    public function create(UserRegistrationDto $dto): void
    {
        $faculty = $this->facultyRepository->find($dto->faculty);

        if ($faculty === null) {
            throw new InvalidFacultySelectedException();
        }

        $user = new User(
            $dto->email,
            $dto->firstName,
            $dto->lastName,
            $faculty,
            $dto->anonymize,
        );

        $user->setPassword($this->hasher->hashPassword($user, $dto->password));

        try {
            $this->userRepository->save($user, true);
        } catch (UniqueConstraintViolationException) {
            throw new NonUniqueEmailException();
        }

        $this->mailer->send($user, new RegisterEmailTemplate());
    }

    /**
     * @return array<string,array<int,string>>
     */
    public function update(User $user, UserEditDto $dto): array
    {
        // update all fields that are not null
        if ($dto->email !== null) {
            $user->email = $dto->email;
        }

        if ($dto->firstName !== null) {
            $user->setFirstName($dto->firstName);
        }

        if ($dto->lastName !== null) {
            $user->setLastName($dto->lastName);
        }

        if ($dto->facultyId !== null) {
            $faculty = $this->facultyRepository->find($dto->facultyId);

            if ($faculty !== null) {
                $user->setFaculty($faculty);
            }
        }

        if ($dto->banned !== null) {
            $user->setBanned($dto->banned);
        }

        if ($dto->roles !== null && \count($dto->roles) !== 0) {
            $user->roles = $dto->roles;
        }

        try {
            $this->userRepository->save($user, true);
        } catch (UniqueConstraintViolationException) {
            return ['email' => ['not_unique']];
        }

        return [];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function updatePassword(
        User $currentUser,
        PasswordChangeDto $dto,
    ): array {
        if (!$this->hasher->isPasswordValid($currentUser, $dto->oldPassword)) {
            return ['old_password' => ['mismatch']];
        }

        $hashedPassword = $this->hasher->hashPassword(
            $currentUser,
            $dto->password,
        );
        $currentUser->setPassword($hashedPassword);

        $this->userRepository->save($currentUser, true);

        return [];
    }

    /**
     * @throws UserNotFoundException
     */
    public function forgottenPasswordRequest(string $email): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($user === null) {
            throw new UserNotFoundException();
        }

        $userPasswordResetToken = \bin2hex(\random_bytes(90));

        $user->setPasswordResetToken($userPasswordResetToken);

        $this->userRepository->save($user, true);

        $forgottenPasswordLink = $this->clientUrlBuilderFactory
            ->builder()
            ->path('/reset-password')
            ->argument($userPasswordResetToken)
            ->build();

        $mail = new ForgottenPasswordEmailTemplate();
        $mail->setContext('password_reset_link', $forgottenPasswordLink);

        $this->mailer->send($user, $mail, forceSend: true);
    }

    public function forgottenPasswordReset(
        User $user,
        #[\SensitiveParameter] ForgottenPasswordResetDto|PasswordResetDto $dto,
    ): void {
        $user->setPassword($this->hasher->hashPassword($user, $dto->password));
        $user->setPasswordResetToken(null);

        $this->userRepository->save($user, true);
    }

    public function updateAnonymization(User $user, bool $anonymize): void
    {
        $user->anonymize = $anonymize;
        $this->userRepository->save($user, true);
    }

    public function disableMailing(string $unsubscribeHash): bool
    {
        $user = $this->userRepository->findByUnsubscribeHash($unsubscribeHash);
        if ($user === null) {
            return false;
        }

        $user->setMailing(false);
        $user->resetEmailUnsubscribeHash();

        $this->userRepository->save($user, true);

        return true;
    }

    public function toggleMailing(User $user, EmailingChangeDto $dto): void
    {
        if ($user->hasMailing() === $dto->mailing) {
            return;
        }

        $user->setMailing($dto->mailing);

        $this->userRepository->save($user, true);
    }

    /**
     * This doesn't actually delete the User, only anonymizes it, so we can keep the submissions and results.
     */
    public function delete(User $user): void
    {
        $this->userRepository->save($user->anonymize(), true);
    }

    /**
     * @throws UserNotFoundException
     * @throws PasswordInvalidException
     */
    public function login(UserLoginDto $dto): User
    {
        $user = $this->userRepository->findOneBy(['email' => $dto->email]);
        if ($user === null) {
            throw new UserNotFoundException();
        }

        if (
            $dto->password === null
            || !$this->hasher->isPasswordValid($user, $dto->password)
        ) {
            throw new PasswordInvalidException();
        }

        if ($dto->firebaseToken !== null) {
            $user->setToken($dto->firebaseToken);
            $this->userRepository->save($user, true);
        }

        return $user;
    }
}
