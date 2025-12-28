<?php

namespace App\Tests\Local;

use App\Repository\SeasonRepository;
use App\Repository\SubmissionRepository;
use App\Services\SubmissionImageValidityCheckerService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LocalOCRTest extends KernelTestCase
{
    public function testImageValidityChecker(): void
    {
        $kernel = self::bootKernel();

        $checker = static::getContainer()->get(SubmissionImageValidityCheckerService::class);
        $submissionRepository = static::getContainer()->get(SubmissionRepository::class);
        $seasonRepository = static::getContainer()->get(SeasonRepository::class);

        $season = $seasonRepository->find(8);

        $accepted = $submissionRepository->findBy([
            'season' => $season,
            'accepted' => true,
        ], limit: 30);

        foreach ($accepted as $submission) {
            if ($submission->image === null) {
                continue;
            }

            if (
                $submission->image->getPath()
                !== '/uploads/660afe10ac0a99.65032747.jpg'
            ) {
                continue;
            }

            $features = $checker->readSubmissionImageData($submission->image);

            if ($features === null) {
                continue;
            }

            printf('image: %s%s', $submission->image->getPath(), \PHP_EOL);

            printf(
                'Features->date: %s vs real: %s%s',
                $features->date?->format('d-m-Y') ?? 'null',
                $submission->date->format('d-m-Y'),
                \PHP_EOL,
            );
            printf(
                'Features->distance: %d m vs real: %d m%s',
                ($features->distance * 1000) ?? 'null',
                $submission->distance,
                \PHP_EOL,
            );
            printf(
                'Features->elevation: %d m vs real: %d m%s',
                $features->elevation ?? 'null',
                $submission->elevation,
                \PHP_EOL,
            );

            echo \PHP_EOL;
        }

        // $rejected = $submissionRepository->findBy([
        //     'season' => $season,
        //     'accepted' => false,
        //     'reviewed' => true,
        // ]);
    }
}
