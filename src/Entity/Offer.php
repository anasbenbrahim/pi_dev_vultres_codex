<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'offre est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le nom de l'offre doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom de l'offre ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[\p{L}0-9\s]+$/u",
        message: "Le nom de l'offre ne peut contenir que des lettres, des chiffres et des espaces."
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le domaine est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le domaine doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le domaine ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[\p{L}0-9\s]+$/u",
        message: "Le domaine ne peut contenir que des lettres, des chiffres et des espaces."
    )]
    private ?string $domain = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de l'offre est obligatoire.")]
    #[Assert\Type(\DateTimeInterface::class, message: "La date doit être valide.")]
    #[Assert\GreaterThanOrEqual("today", message: "La date de l'offre ne peut pas être dans le passé.")]
    private ?\DateTimeInterface $dateOffer = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit contenir au moins {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le nombre de places est obligatoire.")]
    #[Assert\Positive(message: "Le nombre de places doit être un nombre positif.")]
    #[Assert\GreaterThan(value: 0, message: "Le nombre de places doit être strictement positif.")]
    private ?int $nb_Places = null;

    /**
     * @var Collection<int, Demande>
     */
    #[ORM\OneToMany(targetEntity: Demande::class, mappedBy: 'offer', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $offerdemande;

    public function __construct()
    {
        $this->offerdemande = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): static
    {
        $this->domain = $domain;
        return $this;
    }

    public function getDateOffer(): ?\DateTimeInterface  // Naming consistency for getter
    {
        return $this->dateOffer; // Corrected reference
    }

    public function setDateOffer(\DateTimeInterface $dateOffer): static  // Naming consistency for setter
    {
        $this->dateOffer = $dateOffer; // Corrected reference
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getNbPlaces(): ?int  // Naming consistency for getter
    {
        return $this->nb_Places;
    }

    public function setNbPlaces(int $nb_Places): static  // Naming consistency for setter
    {
        $this->nb_Places = $nb_Places;
        return $this;
    }

    /**
     * @return Collection<int, Demande>
     */
    public function getOfferdemande(): Collection
    {
        return $this->offerdemande;
    }

    public function addOfferdemande(Demande $offerdemande): static
    {
        if (!$this->offerdemande->contains($offerdemande)) {
            $this->offerdemande->add($offerdemande);
            $offerdemande->setOffer($this);
        }
        return $this;
    }

    public function removeOfferdemande(Demande $offerdemande): static
    {
        if ($this->offerdemande->removeElement($offerdemande)) {
            if ($offerdemande->getOffer() === $this) {
                $offerdemande->setOffer(null);
            }
        }
        return $this;
    }
}
