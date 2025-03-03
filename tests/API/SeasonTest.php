<?php

namespace App\Tests\API;

use App\CustomLogic\SeasonResult;
use App\Entity\Activity;
use App\Entity\Charity;
use App\Entity\Faculty;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use App\Test\BaseTest;
use Symfony\Component\HttpFoundation\Response;

final class SeasonTest extends BaseTest
{
    public function testCreate(): void
    {
        $this->grantRole(['ROLE_STAFF']);
        $this->makeCharity();

        $charity = $this->getEntityManager()
            ->getRepository(Charity::class)
            ->findOneBy(['name' => 'CharityTest']);

        $today = new \DateTimeImmutable();
        $beginDate = $today->add(new \DateInterval('P1W'));
        $endDate = $today->add(new \DateInterval('P4W'));

        $this->client->jsonRequest('POST', '/api/season', [
            'start' => $beginDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
            'charity' => $charity->getId(),
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCreateNotLoggedIn(): void
    {
        $this->client->jsonRequest('POST', '/api/season', [
            'start' => '2023-07-12',
            'end' => '2023-08-12',
            'charityName' => 'test',
            'charityDescription' => 'test',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testResults(): void
    {
        $this->grantRole(['ROLE_STAFF']);
        $this->makeSeason();

        $seasonRepository = $this->getEntityManager()->getRepository(Season::class);
        $userRepository = $this->getEntityManager()->getRepository(User::class);
        $facultyRepository = $this->getEntityManager()->getRepository(Faculty::class);
        $submissionRepository = $this->getEntityManager()->getRepository(Submission::class);
        $activityRepository = $this->getEntityManager()->getRepository(Activity::class);

        $faculties = $facultyRepository->findAll();
        $user1 = new User('asdf@asdf.com', 'Test1', 'Test1', $faculties[0]);
        $user1->setPassword('pw1');
        $user2 = new User('asdf2@asdf.com', 'Test1', 'Test1', $faculties[1]);
        $user2->setPassword('pw1');

        $userRepository->save($user1, false);
        $userRepository->save($user2, true);

        $activity = $activityRepository->findAll()[0];

        $season = $seasonRepository->getCurrent();

        $sub1 = new Submission($user1, $activity, $season, 'testimage', 12, 125);
        $sub2 = new Submission($user2, $activity, $season, 'testimage', 456, 456);

        $sub1->setReviewed(true);
        $sub2->setReviewed(true);
        $sub1->setAccepted(true);
        $sub2->setAccepted(true);

        $sub1->setWeek(2);
        $sub2->setWeek(2);

        $submissionRepository->save($sub1, false);
        $submissionRepository->save($sub2, true);

        $seasonResult = $this->getContainer()->get(SeasonResult::class);

        $results = $seasonResult->calculate($season);

        $wantedResults = $results[2];

        $this->assertTrue(count($wantedResults) == 1, 'Count mismatch');

        $extras = $wantedResults[0]->extras;

        $this->assertTrue(count($extras) == 2, 'Extras count mismatch');

        $this->assertTrue($extras[0]->value == 456, 'Extras value mismatch');
        $this->assertTrue($extras[1]->value == 456, 'Extras value mismatch');

        $this->assertTrue($extras[0]->faculty == $user2->getFaculty()->getId(), 'Extras faculty mismatch');
        $this->assertTrue($extras[1]->faculty == $user2->getFaculty()->getId(), 'Extras faculty mismatch');

        $this->assertTrue($extras[0]->user == $user2->getId(), 'Extras user mismatch');
        $this->assertTrue($extras[1]->user == $user2->getId(), 'Extras user mismatch');
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
