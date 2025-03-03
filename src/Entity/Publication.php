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

  
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    // New imageChoice property
    #[Assert\Choice(["upload", "url"], message: "Choisissez une méthode valide pour l'image.")]
    private ?string $imageChoice = 'upload'; // Default choice

    #[ORM\ManyToOne(inversedBy: 'publications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null; // Owner of the publication
    
    #[ORM\OneToMany(targetEntity: Rating::class, mappedBy: "publication", cascade: ["remove"])]
    private Collection $ratings;

    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'publication', cascade: ['remove'])]
    private Collection $commentaires;

    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'publication', cascade: ['remove'])]
    private Collection $reclamations;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'publication', cascade: ['remove'])]
    private Collection $notifications;

    public function __construct()
    {
        $this->ratings = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->date = new \DateTime(); // Default date to current time
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

    // New getter and setter for imageChoice
    public function getImageChoice(): ?string
    {
        return $this->imageChoice;
    }

    public function setImageChoice(?string $imageChoice): static
    {
        $this->imageChoice = $imageChoice;
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

    public function setImageUrl(?string $imageUrl): static
{
    $this->image = $imageUrl; 
    return $this;
}

    public function getAverageRating(): float
    {
        $ratings = $this->ratings; 
    
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

    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setPublication($this);
        }
        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            if ($notification->getPublication() === $this) {
                $notification->setPublication(null);
            }
        }
        return $this;
    }
}
