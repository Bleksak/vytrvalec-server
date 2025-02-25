<?php

namespace App\Tests\API;

use App\Test\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class CharityTest extends BaseTest
{
    public function testCreateNotLoggedIn(): void
    {
        $this->client->jsonRequest('POST', '/api/charity', [
            'name' => 'CharityTest',
            'description' => 'CharityTestDescription',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateBasicUser(): void
    {
        $this->grantRole(['ROLE_USER']);

        $this->client->jsonRequest('POST', '/api/charity', [
            'name' => 'CharityTest',
            'description' => 'CharityTestDescription',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreate(): void
    {
        $this->grantRole(['ROLE_STAFF']);

        $this->client->jsonRequest('POST', '/api/charity', [
            'name' => 'CharityTest',
            'description' => 'CharityTestDescription',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    // TODO: test for empty fields
}
