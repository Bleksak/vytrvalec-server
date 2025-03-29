<?php

declare(strict_types=1);

namespace App\Action;

use App\Dto\PasswordResetDto;
use App\Dto\UserAccountChangeDto;
use App\Dto\UserDto;
use App\Dto\UserEditDto;
use App\Entity\User;
use App\Notifications\EmailTemplate\ForgottenPasswordEmailTemplate;
use App\Notifications\EmailTemplate\RegisterEmailTemplate;
use App\Repository\FacultyRepository;
use App\Repository\UserRepository;
use App\Services\VytrvalecMailer;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserActions
{
    public function __construct(
        private UserRepository $userRepository,
        private FacultyRepository $facultyRepository,
        private UserPasswordHasherInterface $hasher,
        private VytrvalecMailer $mailer,
        private ParameterBagInterface $params,
    ) {
    }

    /**
     * @return array<string, array<string>>
     */
    public function create(UserDto $dto): array
    {
        $faculty = $this->facultyRepository->find($dto->faculty);

        if ($faculty === null) {
            return ['faculty' => ['invalid']];
        }

        $user = new User($dto->email, $dto->firstName, $dto->lastName, $faculty, $dto->gdpr);
        $user->setPassword($this->hasher->hashPassword($user, $dto->password));

        try {
            $this->userRepository->save($user, true);
            $this->mailer->send($user, new RegisterEmailTemplate());
        } catch (UniqueConstraintViolationException $e) {
            return ['email' => ['not_unique']];
        }

        return [];
    }

    /**
     * @return array<string,array<int,string>>
     */
    public function update(User $user, UserEditDto $dto): array
    {
        // update all fields that are not null
        if ($dto->email !== null) {
            $user->setEmail($dto->email);
        }

        if ($dto->firstName !== null) {
            $user->setFirstName($dto->firstName);
        }

        if ($dto->lastName !== null) {
            $user->setLastName($dto->lastName);
        }

        if ($dto->faculty !== null) {
            $user->setFaculty($dto->faculty);
        }

        if ($dto->banned !== null) {
            $user->setBanned($dto->banned);
        }

        if ($dto->roles !== null && !empty($dto->roles)) {
            $user->setRoles($dto->roles);
        }

        try {
            $this->userRepository->save($user, true);
        } catch (UniqueConstraintViolationException $e) {
            return ['email' => ['not_unique']];
        }

        return [];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function updateAccount(User $currentUser, UserAccountChangeDto $dto): array
    {
        if ($dto->password === null) {
            return [];
        }

        if (!$this->hasher->isPasswordValid($currentUser, $dto->oldPassword)) {
            return ['old_password' => ['mismatch']];
        }

        if ($dto->password !== null) {
            $hashedPassword = $this->hasher->hashPassword($currentUser, $dto->password);
            $currentUser->setPassword($hashedPassword);
        }

        if ($dto->mailing !== null) {
            $currentUser->setMailing($dto->mailing);
        }

        $this->userRepository->save($currentUser, true);

        return [];
    }

    public function forgottenPasswordRequest(string $email, string $lang): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        $user->setPasswordResetToken(bin2hex(random_bytes(90)));

        $this->userRepository->save($user, true);

        $mail = new ForgottenPasswordEmailTemplate();
        $mail->setContext('password_reset_link', $this->params->get('client_url').'/reset-password/'.$user->getPasswordResetToken());

        $this->mailer->send($user, $mail, true);
    }

    /**
     * @return array<int,string>
     */
    public function forgottenPasswordReset(PasswordResetDto $dto): array
    {
        $user = $this->userRepository->findOneBy(['passwordResetToken' => $dto->passwordResetToken]);

        if ($user === null) {
            return ['user_not_found'];
        }

        $user->setPassword($this->hasher->hashPassword($user, $dto->password));
        $user->setPasswordResetToken(null);

        $this->userRepository->save($user, true);

        return [];
    }

    public function updateGdpr(User $user, bool $gdprValue): void
    {
        $user->setAcceptedGdpr($gdprValue);
        $this->userRepository->save($user, true);
    }

    public function disableMailing(string $unsubscribeHash): bool
    {
        $user = $this->userRepository->findByUnsubscribeHash($unsubscribeHash);
        if ($user === null) {
            return false;
        }

        $user->setMailing(false);
        $user->setEmailUnsubscribeHash(null);

        $this->userRepository->save($user, true);

        return true;
    }
}
