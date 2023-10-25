<?php

namespace App\Tests\API;

use App\Entity\Faculty;
use App\Test\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends BaseTest
{
    public function testRegister(): void
    {
        $faculty = $this->getEntityManager()->getRepository(Faculty::class)->findOneBy(['shortcut' => 'FAV']);

        $this->client->request('POST', '/api/user', [
            "email" => "aasdf@asdf.com",
            "password" => "Qwerty123",
            "firstName" => "string",
            "lastName" => "string",
            "faculty" => $faculty->getId()
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testRegisterDuplicate(): void
    {
        $faculty = $this->getEntityManager()->getRepository(Faculty::class)->findOneBy(['shortcut' => 'FAV']);

        $this->client->request('POST', '/api/user', [
            "email" => "aasdf@asdf.com",
            "password" => "Qwerty123",
            "firstName" => "string",
            "lastName" => "string",
            "faculty" => $faculty->getId()
        ]);

        $this->client->request('POST', '/api/user', [
            "email" => "aasdf@asdf.com",
            "password" => "Qwerty123",
            "firstName" => "string",
            "lastName" => "string",
            "faculty" => $faculty->getId()
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testRegisterInvalidFaculty(): void
    {
        $this->client->request('POST', '/api/user', [
            "email" => "aasdf@asdf.com",
            "password" => "Qwerty123",
            "firstName" => "string",
            "lastName" => "string",
            "faculty" => -1,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testRegisterInvalidEmail(): void
    {
        $faculty = $this->getEntityManager()->getRepository(Faculty::class)->findOneBy(['shortcut' => 'FAV']);

        $this->client->request('POST', '/api/user', [
            "email" => "aasdf@asdf",
            "password" => "Qwerty123",
            "firstName" => "string",
            "lastName" => "string",
            "faculty" => $faculty->getId(),
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testUserLogin(): void
    {
        $this->createUser('aasdf@asdf.com', 'Qwerty123');
        $this->loginUser('aasdf@asdf.com', 'Qwerty123');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHasCookie('jwt');
    }

    public function testUserLoggedInRegister(): void
    {
        $this->createUser('aasdf@asdf.com', 'Qwerty123');
        $this->loginUser('aasdf@asdf.com', 'Qwerty123');
        $this->createUser('aasdf@asdf.com', 'Qwerty123');

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

}