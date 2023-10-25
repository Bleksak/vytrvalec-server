<?php

namespace App\Tests\API;

use App\Test\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class SeasonTest extends BaseTest
{
    // public function testCreate(): void
    // {
    //     $this->grantRole(['ROLE_STAFF']);

    //     $today = new \DateTimeImmutable();
    //     $beginDate = $today->add(new \DateInterval('P1W'));
    //     $endDate = $today->add(new \DateInterval('P4W'));

    //     $this->client->jsonRequest('POST', '/api/season', [
    //         'start' => $beginDate->format('Y-m-d'),
    //         'end' => $endDate->format('Y-m-d'),
    //         'charityName' => 'Test',
    //         'charityDescription' => 'test'
    //     ]);

    //     $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

    // }

    public function testCreateNotLoggedIn(): void
    {
        $this->client->jsonRequest('POST', '/api/season', [
            'start' => '2023-07-12',
            'end' => '2023-08-12',
            'charityName' => 'test',
            'charityDescription' => 'test'
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    // public function testCreateBadDate(): void
    // {
    //     $this->grantRole(['ROLE_STAFF']);

    //     $this->client->jsonRequest('POST', '/api/season', [
    //         'start' => '2023-05-12',
    //         'end' => '2023-08-12',
    //         'charityName' => 'test',
    //         'charityDescription' => 'test'
    //     ]);

    //     $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    // }

    // public function testCreateBadEndDate(): void
    // {
    //     $this->grantRole(['ROLE_STAFF']);

    //     $today = new \DateTimeImmutable();
    //     $beginDate = $today->add(new \DateInterval('P1W'));

    //     $this->client->jsonRequest('POST', '/api/season', [
    //         'start' => $beginDate->format('Y-m-d'),
    //         'end' => '2023-06-12',
    //         'charityName' => 'test',
    //         'charityDescription' => 'test'
    //     ]);

    //     $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    // }

    // public function testDelete(): void
    // {
    //     $this->grantRole(['ROLE_STAFF']);

    //     $today = new \DateTimeImmutable();
    //     $beginDate = $today->add(new \DateInterval('P1W'));
    //     $endDate = $today->add(new \DateInterval('P4W'));

    //     $this->client->jsonRequest('POST', '/api/season', [
    //         'start' => $beginDate->format('Y-m-d'),
    //         'end' => $endDate->format('Y-m-d'),
    //         'charityName' => 'test',
    //         'charityDescription' => 'test'
    //     ]);

    //     $this->client->jsonRequest('GET', '/api/season');
    //     $season = json_decode($this->client->getResponse()->getContent())[2];

    //     $this->client->jsonRequest('DELETE', '/api/season/'.$season->id);
    //     $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    // }
}