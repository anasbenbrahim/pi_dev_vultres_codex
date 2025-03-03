<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RatingRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RatingRepository::class)]
#[ORM\Table(name: "rating")]
class Rating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: Publication::class, inversedBy: "ratings")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Publication $publication = null;

    #[ORM\Column(type: "integer")]
    #[Assert\Range(min: 1, max: 5, notInRangeMessage: "The rating must be between 1 and 5.")]
    private ?int $rating = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getPublication(): ?Publication
    {
        return $this->publication;
    }

    public function setPublication(?Publication $publication): self
    {
        $this->publication = $publication;
        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;
        return $this;
    }
}
