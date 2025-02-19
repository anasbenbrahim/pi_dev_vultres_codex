<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\Status;
use Doctrine\Migrations\Version\State;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    #[Assert\Regex(pattern: "/^[a-zA-Z\s]+$/",message: "Le titre doit contenir uniquement des lettres et des espaces.")]
    #[Assert\Length(max: 50, maxMessage: "Le titre ne doit pas dépasser 50 caractères.")]
    #[Assert\Length(min: 2, minMessage: "Le titre doit contenir au moins 2 caractères.",)]
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(max: 255, maxMessage: "La description ne doit pas dépasser 255 caractères.")]
    #[Assert\Length(min: 2, minMessage: "La description doit contenir au moins 2 caractères.",)]
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Publication $publication = null;

    #[ORM\Column(enumType: Status::class)]
    private ?Status $status = Status::EN_ATTENTE;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getPublication(): ?Publication
    {
        return $this->publication;
    }

    public function setPublication(?Publication $publication): static
    {
        $this->publication = $publication;
        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): static
    {
        $this->status = $status;

        return $this;
    }
}