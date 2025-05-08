<?php

declare(strict_types=1);

namespace App\Tests\API;

use App\Test\BaseTest;

final class FacultyTest extends BaseTest
{
    public function testFetch(): void
    {
        $crawler = $this->client->request('GET', '/api/faculty');

        $this->assertResponseIsSuccessful();
        $this->assertSame($this->client->getResponse()->headers->get('Content-Type'), 'application/json');
    }

    public function testCreate(): void
    {
        $this->grantRole(['ROLE_STAFF']);

        $this->client->request('POST', '/api/faculty', [
            'name' => 'Just A Weird Faculty Passing By',
            'shortcut' => 'JWFP',
            'visible' => true,
        ]);

        $this->assertResponseIsSuccessful();
    }
}
