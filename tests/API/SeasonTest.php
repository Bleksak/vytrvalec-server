<?php

namespace App\Tests\API;

use App\Test\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class SeasonTest extends BaseTest
{
    public function testCreate(): void
    {
        $today = new \DateTimeImmutable();
        $beginDate = $today->add(new \DateInterval('P1W'));
        $endDate = $today->add(new \DateInterval('P4W'));

        $this->grantRole(['ROLE_STAFF']);
        $this->client->jsonRequest('POST', '/api/season/create', [
            'start' => $beginDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
            'charityName' => 'Sbirka na nohu Kasparovy',
            'charityDescription' => 'Mila divenka, jela na vylet do bradavic na koštěti. Koště jí ujelo z mezinoží a napálila do orka. Ork jí zlomil pravou nohu na více způsobů.'
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->client->jsonRequest('POST', '/api/season/create', [
            'start' => $beginDate->format('Y-m-d'),
            'charityName' => 'Sbirka na nohu Kasparovy',
            'charityDescription' => 'Mila divenka, jela na vylet do bradavic na koštěti. Koště jí ujelo z mezinoží a napálila do orka. Ork jí zlomil pravou nohu na více způsobů.'
        ]);
    }

    public function testCreateNotLoggedIn(): void
    {
        $this->client->jsonRequest('POST', '/api/season/create', [
            'start' => '2023-07-12',
            'end' => '2023-08-12',
            'charityName' => 'Sbirka na nohu Kasparovy',
            'charityDescription' => 'Mila divenka, jela na vylet do bradavic na koštěti. Koště jí ujelo z mezinoží a napálila do orka. Ork jí zlomil pravou nohu na více způsobů.'
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateBadDate(): void
    {
        $this->grantRole(['ROLE_STAFF']);
        $this->client->jsonRequest('POST', '/api/season/create', [
            'start' => '2023-05-12',
            'end' => '2023-08-12',
            'charityName' => 'Sbirka na nohu Kasparovy',
            'charityDescription' => 'Mila divenka, jela na vylet do bradavic na koštěti. Koště jí ujelo z mezinoží a napálila do orka. Ork jí zlomil pravou nohu na více způsobů.'
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testCreateBadEndDate(): void
    {
        $this->grantRole(['ROLE_STAFF']);
        $today = new \DateTimeImmutable();
        $beginDate = $today->add(new \DateInterval('P1W'));

        $this->client->jsonRequest('POST', '/api/season/create', [
            'start' => $beginDate->format('Y-m-d'),
            'end' => '2023-06-12',
            'charityName' => 'Sbirka na nohu Kasparovy',
            'charityDescription' => 'Mila divenka, jela na vylet do bradavic na koštěti. Koště jí ujelo z mezinoží a napálila do orka. Ork jí zlomil pravou nohu na více způsobů.'
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testDelete(): void
    {
        $this->grantRole(['ROLE_STAFF']);

        $today = new \DateTimeImmutable();
        $beginDate = $today->add(new \DateInterval('P1W'));
        $endDate = $today->add(new \DateInterval('P4W'));

        $this->client->jsonRequest('POST', '/api/season/create', [
            'start' => $beginDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
            'charityName' => 'Sbirka na nohu Kasparovy',
            'charityDescription' => 'Mila divenka, jela na vylet do bradavic na koštěti. Koště jí ujelo z mezinoží a napálila do orka. Ork jí zlomil pravou nohu na více způsobů.'
        ]);

        $this->client->jsonRequest('GET', '/api/season/list');
        $season = json_decode($this->client->getResponse()->getContent())[2];

        $this->client->jsonRequest('DELETE', '/api/season/'.$season->id.'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}