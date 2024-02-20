<?php

namespace App\Entity;

use App\Repository\FeedbackRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $postedBy = null;

    #[ORM\Column(length: 255)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?int $rating = null;

    #[ORM\Column(length: 32)]
    private ?string $moderation_status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostedBy(): ?string
    {
        return $this->postedBy;
    }

    public function setPostedBy(string $postedBy): static
    {
        $this->postedBy = $postedBy;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getModerationStatus(): ?string
    {
        return $this->moderation_status;
    }

    public function setModerationStatus(string $moderation_status): static
    {
        $this->moderation_status = $moderation_status;

        return $this;
    }
}
