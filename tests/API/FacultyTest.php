<?php

namespace App\Tests\API;

use App\DataFixtures\FacultyFixtures;
use App\Test\BaseTest;

class FacultyTest extends BaseTest
{

    public function testFetch(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/faculty/list');

        $this->assertResponseIsSuccessful();
        $this->assertSame($client->getResponse()->headers->get('Content-Type'), 'application/json');

//        $decoded = json_decode($client->getResponse()->getContent());
//
//        $this->assertCount(9, $decoded);
    }


}