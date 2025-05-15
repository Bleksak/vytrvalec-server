<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\Submission\Response\SubmissionResponseDto;
use App\Repository\SubmissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SubmissionRepository::class)]
#[ORM\Index(columns: ['week'], name: 'week_index')]
class Submission
{
    #[OA\Property(example: 1)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?int $id = null;

    #[OA\Property(example: true)]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private bool $accepted = false;

    #[OA\Property(type: 'integer', example: 1)]
    #[ORM\ManyToOne(inversedBy: 'submissions', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSubmission'])]
    private Season $season;

    #[OA\Property(type: 'integer', example: 1)]
    #[ORM\ManyToOne(inversedBy: 'submissions', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSubmission'])]
    private User $user;

    #[OA\Property(type: 'integer', example: 1500)]
    #[ORM\Column(type: Types::BIGINT)]
    #[Groups(['fetchSubmission'])]
    private string $elevation;

    #[OA\Property(type: 'integer', example: 1500)]
    #[ORM\Column(type: Types::BIGINT)]
    #[Groups(['fetchSubmission'])]
    private string $distance;

    #[OA\Property(example: true)]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private bool $reviewed = false;

    #[OA\Property]
    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: true, referencedColumnName: 'uuid', name: 'image_uuid')]
    #[Groups(['fetchSubmission'])]
    private ?Image $image;

    #[OA\Property(example: 2)]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private int $week;

    #[OA\Property(type: 'integer', example: 1)]
    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSubmission'])]
    private Activity $activity;

    #[OA\Property(type: 'date', example: '2025-04-11')]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['fetchSubmission'])]
    private \DateTime $date;

    #[OA\Property(type: 'datetime', example: 1)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, columnDefinition: 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', updatable: false, insertable: false, generated: 'ALWAYS')]
    #[Groups(['fetchSubmission'])]
    private \DateTime $updatedAt;

    #[OA\Property(example: 'Dobrej vykon lil bro')]
    #[ORM\Column(length: 512)]
    #[Groups(['fetchSubmission'])]
    private string $message = '';

    public function __construct(
        User $user,
        Activity $activity,
        Season $season,
        Image $image,
        int $distance,
        int $elevation = 0,
        string $message = '',
    ) {
        $this->date = new \DateTime();

        $this->user = $user;
        $this->activity = $activity;
        $this->season = $season;
        $this->image = $image;
        $this->distance = (string) $distance;
        $this->elevation = (string) $elevation;
        $this->message = $message;

        $this->calculateWeek();
    }

    public function getId(): int
    {
        return $this->id ?? 0;
    }

    public function isAccepted(): bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function getSeason(): Season
    {
        return $this->season;
    }

    public function setSeason(Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getElevation(): int
    {
        return (int) $this->elevation;
    }

    public function setElevation(int $elevation): self
    {
        $this->elevation = (string) $elevation;

        return $this;
    }

    public function getDistance(): int
    {
        return (int) $this->distance;
    }

    public function setDistance(int $distance): self
    {
        $this->distance = (string) $distance;

        return $this;
    }

    public function isReviewed(): bool
    {
        return $this->reviewed;
    }

    public function setReviewed(bool $reviewed): self
    {
        $this->reviewed = $reviewed;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getActivity(): Activity
    {
        return $this->activity;
    }

    public function setActivity(Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function calculateWeek(): int
    {
        $sub = $this->getDate()->diff($this->getSeason()->getStart());
        $days = $sub->days;

        if ($days === false) {
            $days = 0;
        }

        $this->week = intdiv($days, 7);

        return $this->week;
    }

    public function getWeek(): int
    {
        return $this->week;
    }

    public function setWeek(int $week): static
    {
        $this->week = $week;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function toResponseObject(): SubmissionResponseDto
    {
        return new SubmissionResponseDto(
            $this->getId(),
            $this->isAccepted(),
            $this->getSeason()->getId(),
            $this->getUser()->getId(),
            $this->getElevation(),
            $this->getDistance(),
            $this->isReviewed(),
            $this->getImage()?->getPath(),
            $this->getWeek(),
            $this->getActivity()->getId(),
            $this->getDate(),
            $this->getUpdatedAt(),
            $this->getMessage(),
        );
    }
}
