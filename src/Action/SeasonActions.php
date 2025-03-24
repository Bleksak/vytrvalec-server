<?php

namespace App\Action;

use App\Dto\SeasonDto;
use App\Entity\Season;
use App\Messages\SeasonEndMessage;
use App\Messages\SeasonStartMessage;
use App\Repository\SeasonRepository;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class SeasonActions
{
    public function __construct(
        private readonly SeasonRepository $seasonRepository,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function create(SeasonDto $seasonDto): int
    {
        $existingSeason = $this->seasonRepository->findByStartMonth($seasonDto->start);

        if ($existingSeason !== null) {
            return -1;
        }

        $season = new Season($seasonDto->start, $seasonDto->end, $seasonDto->charity);

        $this->seasonRepository->save($season, true);
        $stamps = [];

        $today = (new \DateTimeImmutable())->setTime(0, 0);
        $diff = $season->getStart()->diff($today)->days;

        if ($diff > 0) {
            $stamps[] = DelayStamp::delayUntil($seasonDto->start);
        }

        $this->messageBus->dispatch(
            new Envelope(
                new SeasonStartMessage($season->getId()),
                $stamps,
            )
        );

        $stamps = [
            DelayStamp::delayUntil($seasonDto->end),
        ];

        $this->messageBus->dispatch(
            new Envelope(
                new SeasonEndMessage($season->getId()),
                $stamps,
            )
        );

        return $season->getId();
    }
}
