<?php

declare(strict_types=1);

namespace App\Action;

use App\Dto\Season\SeasonCreateDto;
use App\Entity\Season;
use App\Messages\SeasonEndMessage;
use App\Messages\SeasonStartMessage;
use App\Repository\CharityRepository;
use App\Repository\SeasonRepository;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final readonly class SeasonActions
{
    public function __construct(
        private SeasonRepository $seasonRepository,
        private CharityRepository $charityRepository,
        private MessageBusInterface $messageBus,
    ) {}

    public function create(SeasonCreateDto $dto): int
    {
        $existingSeason = $this->seasonRepository->findByStartMonth($dto->start);

        if ($existingSeason !== null) {
            return -1;
        }

        $charity = $this->charityRepository->find($dto->charityId);

        if ($charity === null) {
            return -1;
        }

        $season = new Season($dto->start, $dto->end, $charity);

        $this->seasonRepository->save($season, true);
        $stamps = [];

        $today = new \DateTimeImmutable()->setTime(0, 0);
        $diff = $season->getStart()->diff($today)->days;

        assert($diff !== false, 'Diff cannot be false?');

        if ($diff > 0) {
            $stamps[] = DelayStamp::delayUntil($dto->start);
        }

        $this->messageBus->dispatch(new Envelope(new SeasonStartMessage($season->getId()), $stamps));

        $stamps = [
            DelayStamp::delayUntil($dto->end),
        ];

        $this->messageBus->dispatch(new Envelope(new SeasonEndMessage($season->getId()), $stamps));

        return $season->getId();
    }
}
