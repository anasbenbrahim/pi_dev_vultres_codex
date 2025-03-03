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
        max: 255,
        maxMessage: "Le nom ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le domaine est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le domaine ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $domain = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de l'offre est obligatoire.")]
    #[Assert\Type(\DateTimeInterface::class, message: "La date doit être valide.")]
    private ?\DateTimeInterface $date_offer = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "La description ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le nombre de places est obligatoire.")]
    #[Assert\Positive(message: "Le nombre de places doit être un nombre positif.")]
    private ?int $nb_places = null;

    

    

    /**
     * @var Collection<int, Demande>
     */
    #[ORM\OneToMany(targetEntity: Demande::class, mappedBy: 'offer')]
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

    public function getDateOffer(): ?\DateTimeInterface
    {
        return $this->date_offer;
    }

    public function setDateOffer(\DateTimeInterface $date_offer): static
    {
        $this->date_offer = $date_offer;
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

    public function getNbPlaces(): ?int
    {
        return $this->nb_places;
    }

    public function setNbPlaces(int $nb_places): static
    {
        $this->nb_places = $nb_places;
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
