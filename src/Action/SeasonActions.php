<?php

declare(strict_types=1);

namespace App\Action;

use App\Dto\SeasonConfiguration\SeasonConfigurationCreateDto;
use App\Entity\Faculty;
use App\Entity\FacultyMapping;
use App\Entity\Season;
use App\Messages\SeasonEndMessage;
use App\Messages\SeasonStartMessage;
use App\Repository\FacultyMappingRepository;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final readonly class SeasonActions
{
    public function __construct(
        private SeasonRepository $seasonRepository,
        private FacultyMappingRepository $facultyMappingRepository,
        private CharityActions $charityAction,
        private MessageBusInterface $messageBus,
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * @return Season|array<string, array<string, string>|list<string>>
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

        if (is_array($charity)) {
            $this->entityManager->rollback();

            return [
                'charity' => $charity,
            ];
        }

        $season = new Season($dto->season->start, $dto->season->end, $charity);
        $this->seasonRepository->save($season, true);

        foreach ($dto->facultyMapping as $mapping) {
            $faculty = $this->entityManager->getReference(Faculty::class, $mapping->faculty);

            if ($faculty === null) {
                continue;
            }

            $parent = $mapping->parent === null
                ? null
                : $this->entityManager->getReference(Faculty::class, $mapping->parent);

            $mapping = new FacultyMapping($season, $faculty, $parent);
            $this->facultyMappingRepository->save($mapping, false);
        }

        $this->entityManager->flush();

        $this->entityManager->commit();

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
}
