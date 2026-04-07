<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Action\UserActions;


use App\Dto\UserRegistrationDto;
use App\Exceptions\User\InvalidFacultySelectedException;
use App\Exceptions\User\NonUniqueEmailException;
use App\Repository\FacultyRepository;
use App\Repository\UserRepository;
use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Doctrine\ORM\EntityManagerInterface;
use SensitiveParameter;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

final class AuthContext implements Context
{
    use DatabaseContextTrait;

    private $validationErrors = null;
    private ?\Exception $exception = null;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly FacultyRepository $facultyRepository,
        private readonly UserActions $userActions,
        private readonly ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        KernelInterface $kernel,
    ) {
        $this->entityManager = $entityManager;
        $this->container = $kernel->getContainer();
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

        $this->validationErrors = $this->validator->validate($registrationDto);

        if (\count($this->validationErrors) === 0) {
            try {
                $this->userActions->create($registrationDto);
                $this->exception = null;
            } catch (\Exception $e) {
                $this->exception = $e;
            }
        }
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

    #[Then('I should receive a validation error for the :field field')]
    public function iShouldReceiveAValidationErrorForTheField(string $field): void
    {
        assertNotNull($this->validationErrors);
        assertEquals(true, \count($this->validationErrors) > 0);
    }

    #[Then('I should receive an error that the email is already taken')]
    public function iShouldReceiveAnErrorThatTheEmailIsAlreadyTaken(): void
    {
        assertNotNull($this->exception);
        assertInstanceOf(NonUniqueEmailException::class, $this->exception);
    }

    #[Given('there exists a user with email :email')]
    public function thereExistsAUserWithEmail(string $email): void
    {
        assertNotNull($this->userRepository->findOneByEmail($email));
    }

    #[Given('there is no faculty with ID :facultyId')]
    public function thereIsNoFacultyWithId(int $facultyId): void
    {
        assertNull($this->facultyRepository->find($facultyId));
    }

    #[Then('I should receive an error that the faculty does not exist')]
    public function iShouldReceiveAnErrorThatTheFacultyDoesNotExist(): void
    {
        assertNotNull($this->exception);
        assertInstanceOf(
            InvalidFacultySelectedException::class,
            $this->exception,
        );
    }
}
