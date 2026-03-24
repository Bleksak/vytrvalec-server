<?php

declare(strict_types=1);

namespace App\Action;

use App\Algorithm\Graph;
use App\Dto\Faculty\FacultyMappingDto;
use App\Dto\SeasonConfiguration\SeasonConfigurationCreateDto;
use App\Dto\SeasonConfiguration\SeasonConfigurationUpdateDto;
use App\Entity\Faculty;
use App\Entity\FacultyMapping;
use App\Entity\Season;
use App\Exceptions\Season\SeasonCannotBeDeletedException;
use App\Exceptions\SeasonConfiguration\FacultyMappingCycleException;
use App\Repository\CharityRepository;
use App\Repository\FacultyMappingRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubmissionRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class SeasonActions
{
    public function __construct(
        private SeasonRepository $seasonRepository,
        private FacultyMappingRepository $facultyMappingRepository,
        private CharityActions $charityAction,
        private EntityManagerInterface $entityManager,
        private SubmissionRepository $submissionRepository,
        private CharityRepository $charityRepository,
    ) {}

    /**
     * @return Season|array<string, array<string, string>|list<string>>
     * @throws FacultyMappingCycleException
     */
    public function create(SeasonConfigurationCreateDto $dto): Season|array
    {
        $existingSeason = $this->seasonRepository->findByStartMonth($dto->season->start);

        if ($existingSeason !== null) {
            return [
                'season' => ['running'],
            ];
        }

        $this->entityManager->beginTransaction();

        $charity = $this->charityAction->create($dto->charity);

        if (\is_array($charity)) {
            $this->entityManager->rollback();

            return [
                'charity' => $charity,
            ];
        }

        $season = new Season(
            $dto->season->start,
            $dto->season->end,
            $charity,
            $dto->season->isTest,
        );
        $this->seasonRepository->save($season, true);

        try {
            $this->applyFacultyMappings($season, $dto->facultyMapping);
        } catch (FacultyMappingCycleException $e) {
            $this->entityManager->rollback();

            throw $e;
        }

        $this->entityManager->flush();

        $this->entityManager->commit();

        // TODO(@bleksak): send notifications
        //
        // if ($dto->season->notificationDate !== null) {
        //     $stamps[] = DelayStamp::delayUntil($dto->season->start);
        //     $this->messageBus->dispatch(
        //         new Envelope(new SeasonStartMessage($season->getId()), [
        //             DelayStamp::delayUntil($dto->season->notificationDate),
        //         ]),
        //     );
        // }

        // $stamps = [
        //     DelayStamp::delayUntil($dto->season->notificationDate),
        // ];

        // $this->messageBus->dispatch(new Envelope(new SeasonEndMessage($season->getId()), $stamps));

        return $season;
    }

    public function update(
        Season $season,
        SeasonConfigurationUpdateDto $dto,
    ): void {
        $season->start = $dto->season->start;
        $season->end = $dto->season->end;
        $season->isTest = $dto->season->isTest;

        $this->facultyMappingRepository->removeBySeason($season);
        $this->applyFacultyMappings($season, $dto->facultyMapping);

        $this->entityManager->flush();
    }

    /**
     * @param list<FacultyMappingDto> $mappings
     * @throws FacultyMappingCycleException
     */
    private function applyFacultyMappings(Season $season, array $mappings): void
    {
        $graph = [];
        $nodes = [];

        foreach ($mappings as $mapping) {
            if ($mapping->parent !== null) {
                $graph[$mapping->faculty][] = $mapping->parent;
                $nodes[$mapping->parent] = true;
            }

            $nodes[$mapping->faculty] = true;

            $faculty = $this->entityManager->getReference(
                Faculty::class,
                $mapping->faculty,
            );

            if ($faculty === null) {
                continue;
            }

            $parent = $mapping->parent === null
                ? null
                : $this->entityManager->getReference(
                    Faculty::class,
                    $mapping->parent,
                );

            $newMapping = new FacultyMapping($season, $faculty, $parent);
            $this->facultyMappingRepository->save($newMapping, false);
        }

        if (Graph::hasCycle(\array_keys($nodes), $graph)) {
            throw new FacultyMappingCycleException();
        }
    }

    public function delete(Season $season): void
    {
        if (!$season->canDelete()) {
            throw new SeasonCannotBeDeletedException();
        }

        $this->entityManager->beginTransaction();

        if ($season->isTest) {
            $this->facultyMappingRepository->removeBySeason($season);
            $this->submissionRepository->removeBySeason($season);
            $this->charityRepository->remove($season->charity);
        }

        $this->seasonRepository->remove($season, true);

        $this->entityManager->commit();
    }
}
