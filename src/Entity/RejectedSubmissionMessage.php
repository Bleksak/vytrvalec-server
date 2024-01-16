<?php

namespace App\Entity;

use App\Repository\RejectedSubmissionMessageRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RejectedSubmissionMessageRepository::class)]
class RejectedSubmissionMessage
{
    #[ORM\Id]
    #[ORM\OneToOne(mappedBy: 'id', targetEntity: Submission::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private ?Submission $submission = null;

    #[ORM\Column(length: 512)]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $createdAt;

    public function __construct(Submission $submission, string $message)
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->submission = $submission;
        $this->message = $message;
    }

    public function getSubmission(): ?Submission
    {
        return $this->submission;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getTimeCreated(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
