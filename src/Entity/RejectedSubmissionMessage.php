<?php

namespace App\Entity;

use App\Repository\RejectedSubmissionMessageRepository;
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

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function setSubmission(Submission $submission): static
    {
        $this->submission = $submission;

        return $this;
    }
    public function getSubmission(): ?Submission
    {
        return $this->submission;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getTimeCreated(): ?\DateTimeInterface
    {
        return $this->time_created;
    }

    public function setTimeCreated(\DateTimeInterface $time_created): static
    {
        $this->time_created = $time_created;

        return $this;
    }
}
