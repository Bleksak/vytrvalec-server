<?php

namespace App\Action;

use App\Dto\UserAccountChangeDto as AppUserAccountChangeDto;
use App\Dto\UserDto;
use App\Dto\UserEditDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use UserAccountChangeDto;

class UserActions
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $hasher,
    )
    {
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
        } catch(UniqueConstraintViolationException $e) {
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

        if($dto->banned !== null) {
            $user->setBanned($dto->banned);
        }

        if($dto->roles !== null && !empty($dto->roles)) {
            $user->setRoles($dto->roles);
        }

        try {
            $this->userRepository->save($user, true);
        } catch(UniqueConstraintViolationException $e) {
            return ['email' => ['not_unique']];
        }

        return [];
    }
    /**
     * @return array<string, array<int, string>>
     */
    public function updateAccount(User $currentUser, AppUserAccountChangeDto $dto): array
    {
        if($dto->email === null && $dto->password === null) {
            return ['email' => ['blank'], 'password' => ['blank']];
        }

        if(!$this->hasher->isPasswordValid($currentUser, $dto->oldPassword)) {
            return ['old_password' => ['mismatch']];
        }

        $hashedPassword = $this->hasher->hashPassword($currentUser, $dto->password);
        $currentUser->setPassword($hashedPassword);

        try {
            $this->userRepository->save($currentUser, true);
        }
        catch(UniqueConstraintViolationException $e) {
            return ['email' => ['not_unique']];
        }

        return [];
    }
}
