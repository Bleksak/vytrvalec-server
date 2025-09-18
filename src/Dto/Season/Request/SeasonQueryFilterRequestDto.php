<?php

declare(strict_types=1);

namespace App\Dto\Season\Request;

final readonly class SeasonQueryFilterRequestDto
{
    public function __construct(
        public ?\DateTime $date = null,
        public ?int $week = null,
        public ?bool $accepted = null,
        public ?bool $reviewed = null,
        public ?string $user = null,
        public ?int $faculty = null,
        public ?int $activity = null,
        public ?int $page = null,
    ) {}

    /**
     * @return array<string, int|string|\DateTime>
     */
    public function toArray(): array
    {
        /** @var array<string, int|string|\DateTime> */
        return array_filter(
            [
                SeasonQueryFilterType::Date->value => $this->date,
                SeasonQueryFilterType::Week->value => $this->week,
                SeasonQueryFilterType::Accepted->value => $this->accepted,
                SeasonQueryFilterType::Reviewed->value => $this->reviewed,
                SeasonQueryFilterType::User->value => $this->user,
                SeasonQueryFilterType::Faculty->value => $this->faculty,
                SeasonQueryFilterType::Activity->value => $this->activity,
                SeasonQueryFilterType::Page->value => $this->page,
            ],
        );
    }
}
