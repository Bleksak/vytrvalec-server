<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\Submission\Response\SubmissionResponseDto;
use App\Repository\SubmissionRepository;
use App\Services\ImagePath;
use App\Utils\SubmissionState;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubmissionRepository::class)]
#[ORM\Index(columns: ['week'], name: 'week_index')]
#[ORM\Index(columns: [
    'state',
    'season_id',
    'activity_id',
    'user_id',
    'distance',
], name: 'idx_submission_state_season_activity_user_distance')]
#[ORM\Index(
    columns: ['state', 'activity_id'],
    name: 'idx_submission_state_activity',
)]
#[ORM\Index(
    columns: ['state', 'season_id', 'user_id'],
    name: 'idx_submission_state_season_user',
)]
#[ORM\Index(
    columns: ['season_id', 'week', 'state'],
    name: 'idx_submission_season_week_state',
)]
#[ORM\Index(
    columns: ['state', 'activity_id', 'distance'],
    name: 'idx_submission_state_activity_distance',
)]
#[ORM\HasLifecycleCallbacks]
final class Submission extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\ManyToOne(inversedBy: 'submissions')]
    #[ORM\JoinColumn(nullable: false)]
    public Season $season;

    #[ORM\ManyToOne(inversedBy: 'submissions')]
    #[ORM\JoinColumn(nullable: false)]
    public private(set) User $user;

    #[ORM\Column(type: Types::BIGINT)]
    public int $elevation;

    #[ORM\Column(type: Types::BIGINT)]
    public int $distance;

    #[ORM\Column]
    public SubmissionState $state = SubmissionState::Pending;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        nullable: true,
        referencedColumnName: 'uuid',
        name: 'image_uuid',
    )]
    public ?Image $image;

    #[ORM\Column]
    public int $week;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    public Activity $activity;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public \DateTime $date;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public \DateTime $updatedAt;

    #[ORM\Column(length: 512)]
    public string $message = '';

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function __construct(
        User $user,
        Activity $activity,
        Season $season,
        Image $image,
        int $distance,
        \DateTime $date,
        int $elevation = 0,
    ) {
        $this->date = $date;

        $this->user = $user;
        $this->activity = $activity;
        $this->season = $season;
        $this->image = $image;
        $this->distance = $distance;
        $this->elevation = $elevation;
        $this->message = '';

        $sub = $this->date->diff($this->season->start);
        $days = $sub->days;

        if ($days === false) {
            $days = 0;
        }

        $this->week = \intdiv($days, num2: 7);
        $this->updatedAt = new \DateTime();
    }

    public function isEditable(): bool
    {
        return $this->state === SubmissionState::Pending;
    }

    public function toResponseObject(?ImagePath $imagePath): SubmissionResponseDto
    {
        return new SubmissionResponseDto(
            $this->id,
            $this->season->id,
            $this->user->id,
            $this->elevation,
            $this->distance,
            $this->state,
            $this->image?->getPath($imagePath),
            $this->week,
            $this->activity->id,
            $this->date,
            $this->updatedAt,
            $this->image?->uuid->toString(),
            $this->message,
        );
    }
}
