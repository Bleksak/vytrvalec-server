<?php

namespace App\Action;

use App\Dto\PasswordResetDto;
use App\Dto\UserAccountChangeDto;
use App\Dto\UserDto;
use App\Dto\UserEditDto;
use App\Entity\User;
use App\Notifications\EmailTemplate\ForgottenPasswordEmailTemplate;
use App\Notifications\EmailTemplate\RegisterEmailTemplate;
use App\Notifications\VytrvalecEmail;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserActions
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $hasher,
        private MailerInterface $mailer,
        private ParameterBagInterface $params,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function create(UserDto $dto): array
    {
        $user = new User($dto->email, $dto->firstName, $dto->lastName, $dto->faculty);
        $user->setPassword($this->hasher->hashPassword($user, $dto->password));

        try {
            $this->userRepository->save($user, true);
            $this->mailer->send(new VytrvalecEmail($dto->email, new RegisterEmailTemplate()));
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
        if ($dto->email === null && $dto->password === null) {
            return ['email' => ['blank'], 'password' => ['blank']];
        }

        if (!$this->hasher->isPasswordValid($currentUser, $dto->oldPassword)) {
            return ['old_password' => ['mismatch']];
        }

        $hashedPassword = $this->hasher->hashPassword($currentUser, $dto->password);
        $currentUser->setPassword($hashedPassword);

        try {
            $this->userRepository->save($currentUser, true);
        } catch (UniqueConstraintViolationException $e) {
            return ['email' => ['not_unique']];
        }

        return [];
    }

    public function forgottenPasswordRequest(string $email, string $lang): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        $user->setPasswordResetToken(bin2hex(random_bytes(90)));

        $this->userRepository->save($user, true);

        $mail = new ForgottenPasswordEmailTemplate();
        $mail->setContext('password_reset_link', $this->params->get('client_url').'/reset-password/'.$user->getPasswordResetToken());

        $this->mailer->send(new VytrvalecEmail($email, $mail));
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
}
