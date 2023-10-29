<?php

namespace App\Action;

use App\Dto\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
            return ['not_unique_email'];
        }

        return [];
    }
}
