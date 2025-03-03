<?php

namespace App\Tests\API;

use App\Entity\Faculty;
use App\Test\BaseTest;
use Symfony\Component\HttpFoundation\Response;

final class UserTest extends BaseTest
{
    public const PASSWORD = 'Qwertaz!1231@pepega';

    public function testRegister(): void
    {
        $faculty = $this->getEntityManager()
            ->getRepository(Faculty::class)
            ->findOneBy(['shortcut' => 'FAV']);

        $this->client->request('POST', '/api/user', [
            'email' => 'aasdf@asdf.com',
            'password' => self::PASSWORD,
            'first_name' => 'string',
            'last_name' => 'string',
            'faculty' => $faculty->getId(),
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testRegisterDuplicate(): void
    {
        $faculty = $this->getEntityManager()
            ->getRepository(Faculty::class)
            ->findOneBy(['shortcut' => 'FAV']);

        $this->client->request('POST', '/api/user', [
            'email' => 'aasdf@asdf.com',
            'password' => self::PASSWORD,
            'first_name' => 'string',
            'last_name' => 'string',
            'faculty' => $faculty->getId(),
        ]);

        $this->client->request('POST', '/api/user', [
            'email' => 'aasdf@asdf.com',
            'password' => self::PASSWORD,
            'first_name' => 'string',
            'last_name' => 'string',
            'faculty' => $faculty->getId(),
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testRegisterInvalidFaculty(): void
    {
        $this->client->request('POST', '/api/user', [
            'email' => 'aasdf@asdf.com',
            'password' => self::PASSWORD,
            'first_name' => 'string',
            'last_name' => 'string',
            'faculty' => -1,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testRegisterInvalidEmail(): void
    {
        $faculty = $this->getEntityManager()
            ->getRepository(Faculty::class)
            ->findOneBy(['shortcut' => 'FAV']);

        $this->client->request('POST', '/api/user', [
            'email' => 'aasdf@asdf',
            'password' => self::PASSWORD,
            'first_name' => 'string',
            'last_name' => 'string',
            'faculty' => $faculty->getId(),
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testUserLogin(): void
    {
        $this->createUser('aasdf@asdf.com', self::PASSWORD);
        $this->loginUser('aasdf@asdf.com', self::PASSWORD);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHasCookie('jwt');
    }

    public function testUserLoggedInRegister(): void
    {
        $this->createUser('aasdf@asdf.com', self::PASSWORD);
        $this->loginUser('aasdf@asdf.com', self::PASSWORD);
        $this->createUser('aasdf@asdf.com', self::PASSWORD);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
