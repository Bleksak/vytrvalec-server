<?php

namespace App\Tests\API;

use App\Entity\Activity;
use App\Test\BaseTest;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class SubmissionTest extends BaseTest
{
    public function testUpload(): void
    {
        $this->grantRole(['ROLE_STAFF']);
        $this->makeCharity();
        $this->makeSeason();

        $uploadedFile = new UploadedFile(__DIR__ . '/huba.jpg', 'huba.jpg', test: true);
        $activities = $this->getEntityManager()->getRepository(Activity::class)->findAll();
       
        $this->client->request('POST', '/api/submission', [
            "distance" => 100,
            "elevation" => 100,
            "activity" => $activities[0]->getId(),
        ], [
            'image' => $uploadedFile
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }
}
