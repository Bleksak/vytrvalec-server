<?php

declare(strict_types=1);

namespace App\Tests\API;

use App\Entity\Activity;
use App\Entity\Season;
use App\Entity\Submission;
use App\Test\BaseTest;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

final class SubmissionTest extends BaseTest
{
    public function testUpload(): void
    {
        $this->grantRole(['ROLE_STAFF']);
        $this->makeCharity();
        $this->makeSeason();

        // $uploadedFile = new UploadedFile(__DIR__ . '/houba.jpg', 'huba.jpg');
        $uploadedFile = $this->getUploadedFile('houba.jpg');
        $activities = $this->getEntityManager()
            ->getRepository(Activity::class)
            ->findAll();

        $this->client->request('POST', '/api/submission', [
            'distance' => 100,
            'elevation' => 100,
            'activity' => $activities[0]->getId(),
        ], [
            'image' => $uploadedFile,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testPatch(): void
    {
        $this->grantRole(['ROLE_STAFF']);
        $this->makeCharity();
        $this->makeSeason();

        $uploadedFile = $this->getUploadedFile('houba.jpg');

        $activities = $this->getEntityManager()
            ->getRepository(Activity::class)
            ->findAll();

        $this->client->request('POST', '/api/submission', [
            'distance' => 100,
            'elevation' => 100,
            'activity' => $activities[0]->getId(),
        ], [
            'image' => $uploadedFile,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $submission = $this->getEntityManager()
            ->getRepository(Submission::class)
            ->findOneBy(['activity' => $activities[0], 'distance' => 100, 'elevation' => 100]);

        $this->client->request(
            'PATCH',
            '/api/submission/'.$submission->getId(),
            [
                'distance' => 120,
                'updated_at' => $submission->getUpdatedAt()->format('Y-m-d H:i:s'),
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->client->request(
            'PATCH',
            '/api/submission/'.$submission->getId().'/state',
            [
                'updated_at' => $submission->getUpdatedAt()->format('Y-m-d H:i:s'),
                'state' => true,
                'message' => '',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testOrdering(): void
    {
        $this->grantRole(['ROLE_STAFF']);
        $this->makeCharity();
        $this->makeSeason();

        $uploadedFile = $this->getUploadedFile('houba.jpg');
        $activities = $this->getEntityManager()
            ->getRepository(Activity::class)
            ->findAll();

        $this->client->request('POST', '/api/submission', [
            'distance' => 100,
            'elevation' => 100,
            'activity' => $activities[0]->getId(),
        ], [
            'image' => $uploadedFile,
        ]);

        $uploadedFile = $this->getUploadedFile('houba.jpg');
        sleep(1);

        $this->client->request('POST', '/api/submission', [
            'distance' => 100,
            'elevation' => 100,
            'activity' => $activities[0]->getId(),
        ], [
            'image' => $uploadedFile,
        ]);

        $currentSeason = $this->getEntityManager()
            ->getRepository(Season::class)
            ->getCurrent();

        $this->client->request('GET', '/api/submission/list/'.$currentSeason->getId().'/1');
        $response = json_decode($this->client->getResponse()->getContent())->submissions;

        $this->assertTrue($response[0]->date > $response[1]->date, 'Submission ordering is incorrect');
    }
}
