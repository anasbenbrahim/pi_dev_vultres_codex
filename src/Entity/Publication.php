<?php

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PublicationRepository::class)]
class Publication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    #[Assert\Regex(pattern: "/^[a-zA-Z\s]+$/", message: "Le titre doit contenir uniquement des lettres et des espaces.")]
    #[Assert\Length(max: 50, maxMessage: "Le titre ne doit pas dépasser 50 caractères.")]
    #[Assert\Length(min: 2, minMessage: "Le titre doit contenir au moins 2 caractères.")]
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(max: 255, maxMessage: "La description ne doit pas dépasser 255 caractères.")]
    #[Assert\Length(min: 2, minMessage: "La description doit contenir au moins 2 caractères.")]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[Assert\NotBlank(message: "La date ne peut pas être vide.")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[Assert\NotBlank(message: "L'URL de l'image est obligatoire.")]
    #[Assert\Url(message: "L'URL de l'image doit être valide.")]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    private ?Client $client = null;
    
    #[ORM\OneToMany(targetEntity: Rating::class, mappedBy: "publication", cascade: ["remove"])]
    private Collection $ratings;

    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'publication', cascade: ['remove'])]
    private Collection $commentaires;

    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'publication', cascade: ['remove'])]
    private Collection $reclamations;

    public function __construct()
    {
        $this->ratings = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->date = new \DateTime();
    }

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;
        return $this;
    }

    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function getAverageRating(): float
    {
        $ratings = $this->ratings; // Assuming $ratings is a collection of Rating entities
    
        if ($ratings->isEmpty()) {
            return 0;
        }
    
        $total = array_reduce($ratings->toArray(), fn($sum, $rating) => $sum + $rating->getRating(), 0);
        return $total / count($ratings);
    }
    


    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function __toString(): string
    {
        return $this->titre;
    }
}
