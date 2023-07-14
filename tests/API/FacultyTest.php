<?php

namespace App\Tests\API;

use App\Entity\Faculty;
use App\Entity\User;
use App\Test\BaseTest;

class FacultyTest extends BaseTest
{

    public function testFetch(): void
    {
        $crawler = $this->client->request('GET', '/api/faculty/list');

        $this->assertResponseIsSuccessful();
        $this->assertSame($this->client->getResponse()->headers->get('Content-Type'), 'application/json');
    }

    public function testCreate(): void
    {
        $faculty = $this->getEntityManager()->getRepository(Faculty::class)->findOneBy(['shortcut' => 'FAV']);

        $this->client->request('POST', '/api/user/register', [
            "email" => "aasdf@asdf.com",
            "password" => "Qwerty123",
            "firstName" => "string",
            "lastName" => "string",
            "faculty" => $faculty->getId(),
        ]);

        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => 'aasdf@asdf.com']);
        $user->setRoles(['ROLE_STAFF']);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        $this->client->request('POST', '/api/user/login', [
            'email' => 'aasdf@asdf.com',
            'password' => 'Qwerty123'
        ]);

        $this->client->request('POST', '/api/faculty/create', [
            'name' => 'Just A Weird Faculty Passing By',
            'shortcut' => 'JWFP',
            'visible' => true
        ]);


        $this->assertResponseIsSuccessful();
    }
}