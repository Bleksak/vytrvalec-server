<?php

declare(strict_types=1);

namespace App\Dto\Season\Request;

use App\Utils\SubmissionState;
use Doctrine\Common\Collections\Criteria;

final readonly class SeasonQueryFilterRequestDto
{
    public function __construct(
        public ?\DateTime $date = null,
        public ?int $week = null,
        public ?SubmissionState $state = null,
        public ?string $user = null,
        public ?int $faculty = null,
        public ?int $activity = null,
        public ?int $page = null,
    ) {}

    /**
     * @return array<value-of<SeasonQueryFilterType>, bool|int|string|\DateTime|SubmissionState>
     */
    public function toArray(): array
    {
        return \array_filter([
            SeasonQueryFilterType::Date->value => $this->date,
            SeasonQueryFilterType::Week->value => $this->week,
            SeasonQueryFilterType::State->value => $this->state,
            SeasonQueryFilterType::User->value => $this->user,
            SeasonQueryFilterType::Faculty->value => $this->faculty,
            SeasonQueryFilterType::Activity->value => $this->activity,
        ]);
    }

    public function toCriteria(): Criteria
    {
        $criteria = Criteria::create();

        if ($this->date !== null) {
            $criteria->andWhere(Criteria::expr()->eq(
                SeasonQueryFilterType::Date->value,
                $this->date,
            ));
        }

        if ($this->week !== null) {
            $criteria->andWhere(Criteria::expr()->eq(
                SeasonQueryFilterType::Week->value,
                $this->week,
            ));
        }

        if ($this->state !== null) {
            $criteria->andWhere(Criteria::expr()->eq(
                SeasonQueryFilterType::State->value,
                $this->state,
            ));
        }

        if ($this->user !== null) {
            $criteria->andWhere(Criteria::expr()->contains(
                'u.email',
                $this->user,
            ));
        }

        if ($this->faculty !== null) {
            $criteria->andWhere(Criteria::expr()->eq(
                'u.faculty',
                $this->faculty,
            ));
        }

        if ($this->activity !== null) {
            $criteria->andWhere(Criteria::expr()->eq(
                's.activity',
                $this->activity,
            ));
        }

        return $criteria;
    }
}
