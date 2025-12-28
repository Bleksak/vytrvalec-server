<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\Submission\Response\SubmissionResponseDto;
use App\Repository\SubmissionRepository;
use App\Services\ImagePath;
use App\Utils\SubmissionState;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: SubmissionRepository::class)]
#[ORM\Index(columns: ['week'], name: 'week_index')]
#[ORM\HasLifecycleCallbacks]
final class Submission
{
    #[OA\Property(example: 1)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[OA\Property(example: true)]
    #[ORM\Column]
    public bool $accepted = false;

    #[OA\Property(type: 'integer', example: 1)]
    #[ORM\ManyToOne(inversedBy: 'submissions')]
    #[ORM\JoinColumn(nullable: false)]
    public Season $season;

    #[OA\Property(type: 'integer', example: 1)]
    #[ORM\ManyToOne(inversedBy: 'submissions')]
    #[ORM\JoinColumn(nullable: false)]
    public private(set) User $user;

    #[OA\Property(type: 'integer', example: 1500)]
    #[ORM\Column(type: Types::BIGINT)]
    public int $elevation;

    #[OA\Property(type: 'integer', example: 1500)]
    #[ORM\Column(type: Types::BIGINT)]
    public int $distance;

    #[OA\Property(example: true)]
    #[ORM\Column]
    public bool $reviewed = false;

    #[OA\Property]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        nullable: true,
        referencedColumnName: 'uuid',
        name: 'image_uuid',
    )]
    public ?Image $image;

    #[OA\Property(example: 2)]
    #[ORM\Column]
    public int $week;

    #[OA\Property(type: 'integer', example: 1)]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    public Activity $activity;

    #[OA\Property(type: 'string', format: 'date', example: '2025-04-11')]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public \DateTime $date;

    #[OA\Property(type: 'string', format: 'date-time', example: 1)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public \DateTime $updatedAt;

    #[OA\Property(example: 'Dobrej vykon lil bro')]
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
    }

    public function getState(): SubmissionState
    {
        if ($this->reviewed === false) {
            return SubmissionState::Pending;
        }

        return $this->accepted
            ? SubmissionState::Accepted
            : SubmissionState::Rejected;
    }

    public function isEditable(): bool
    {
        return $this->reviewed === false || $this->accepted === false;
    }

    public function toResponseObject(?ImagePath $imagePath): SubmissionResponseDto
    {
        return new SubmissionResponseDto(
            $this->id,
            $this->accepted,
            $this->season->id,
            $this->user->id,
            $this->elevation,
            $this->distance,
            $this->reviewed,
            $this->image?->getPath($imagePath),
            $this->week,
            $this->activity->id,
            $this->date,
            $this->updatedAt,
            $this->message,
        );
    }
}
