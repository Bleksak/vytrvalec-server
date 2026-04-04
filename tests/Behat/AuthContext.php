<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Action\UserActions;
use App\Dto\UserRegistrationDto;
use App\Repository\FacultyRepository;
use App\Repository\UserRepository;
use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Doctrine\ORM\EntityManagerInterface;
use SensitiveParameter;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

final class AuthContext implements Context
{
    use DatabaseContextTrait;

    public function __construct(
        EntityManagerInterface $em,
        private readonly UserRepository $userRepository,
        private readonly FacultyRepository $facultyRepository,
        private readonly UserActions $userActions,
    ) {
        $this->entityManager = $em;
    }

    #[Given('there is no user registered with email :email')]
    public function thereIsNoUserRegisteredWithEmail(string $email): void
    {
        assertNull($this->userRepository->findOneByEmail($email));
    }

    #[Given('there exists a faculty with ID :faucultyId')]
    public function thereExistsAFacultyWithId(int $facultyId): void
    {
        assertNotNull($this->facultyRepository->find($facultyId));
    }

    #[When(
        'I send the registration form with email :email, password: :password, first name: :firstName, last name: :lastName, facultyId: :facultyId, anonymize: :anonymize, gdpr: :gdpr',
    )]
    public function iSendTheRegistrationFormWithEmailPasswordFirstNameLastNameFacultyidAnonymizeGdpr(
        string $email,
        #[SensitiveParameter]
        string $password,
        string $firstName,
        string $lastName,
        int $facultyId,
        string $anonymize,
        string $gdpr,
    ): void {
        $anonymize = \filter_var($anonymize, FILTER_VALIDATE_BOOL);
        $gdpr = \filter_var($gdpr, FILTER_VALIDATE_BOOL);

        $registrationDto = new UserRegistrationDto(
            $email,
            $password,
            $firstName,
            $lastName,
            $facultyId,
            $anonymize,
            $gdpr,
        );

        $this->userActions->create($registrationDto);
    }

    #[Then(
        'User with the email :email should exist in the database, with the first name: :firstName, last name: :lastName, faculty id: :facultyId',
    )]
    public function userWithTheEmailShouldExistInTheDatabaseWithTheFirstNameLastNameFacultyId(
        string $email,
        string $firstName,
        string $lastName,
        int $faculyId,
    ): void {
        $user = $this->userRepository->findOneByEmail($email);

        assertNotNull($user);
        assertEquals($firstName, $user->firstName);
        assertEquals($lastName, $user->lastName);
        assertEquals($faculyId, $user->faculty->id);
    }
}
