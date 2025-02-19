<?php

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Datecontrol;


#[ORM\Entity(repositoryClass: PublicationRepository::class)]
class Publication
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
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[Assert\NotBlank(message: "La date ne peut pas être vide.")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[Assert\NotBlank(message: "L'URL de l'image est obligatoire.")]
    #[Assert\Url(message: "L'URL de l'image doit être valide.")]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;
    #[ORM\Column(type: 'integer', nullable: true)]
private ?int $rating = null;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    private ?Client $client = null;
    

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'publication', cascade: ['remove'])]
    private Collection $commentaires;

    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'publication', cascade: ['remove'])]
    private Collection $reclamations;

    /**
     * @var Collection<int, Notification>
     */
   

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->date = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRating(): ?int
{
    return $this->rating;
}

public function setRating(?int $rating): self
{
    $this->rating = $rating;
    return $this;
}

    public function getTitre()
    {
        return $this->titre;
    }

    public function __toString(): string
    {
        return $this->titre;
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

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setPublication($this);
        }
        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            if ($commentaire->getPublication() === $this) {
                $commentaire->setPublication(null);
            }
        }
        return $this;
    }

    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): static
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setPublication($this);
        }
        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): static
    {
        if ($this->reclamations->removeElement($reclamation)) {
            if ($reclamation->getPublication() === $this) {
                $reclamation->setPublication(null);
            }
        }
        return $this;
    }

   
}