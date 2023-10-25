<?php

namespace App\Action;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Requests\UserCreateRequest;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserActions
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $hasher,
    )
    {
    }

    public function create(UserCreateRequest $request): void
    {
        $user = new User($request->getEmail(), $request->getFirstName(), $request->getLastName(), $request->getFaculty());

        $user->setPassword($this->hasher->hashPassword($user, $request->getPassword()));
        
        $this->userRepository->save($user, true);
    }
}
