<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $domain = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_offer = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $nb_places = null;

    #[ORM\ManyToOne]
    private ?Fermier $fermieroffer = null;

    /**
     * @var Collection<int, Employee>
     */
    #[ORM\ManyToMany(targetEntity: Employee::class, inversedBy: 'offers')]
    private Collection $employeeoffer;

    /**
     * @var Collection<int, Demande>
     */
    #[ORM\OneToMany(targetEntity: Demande::class, mappedBy: 'offer')]
    private Collection $offerdemande;

    public function __construct()
    {
        $this->employeeoffer = new ArrayCollection();
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

    public function getFermieroffer(): ?Fermier
    {
        return $this->fermieroffer;
    }

    public function setFermieroffer(?Fermier $fermieroffer): static
    {
        $this->fermieroffer = $fermieroffer;

        return $this;
    }

    /**
     * @return Collection<int, Employee>
     */
    public function getEmployeeoffer(): Collection
    {
        return $this->employeeoffer;
    }

    public function addEmployeeoffer(Employee $employeeoffer): static
    {
        if (!$this->employeeoffer->contains($employeeoffer)) {
            $this->employeeoffer->add($employeeoffer);
        }

        return $this;
    }

    public function removeEmployeeoffer(Employee $employeeoffer): static
    {
        $this->employeeoffer->removeElement($employeeoffer);

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
            // set the owning side to null (unless already changed)
            if ($offerdemande->getOffer() === $this) {
                $offerdemande->setOffer(null);
            }
        }

        return $this;
    }
}
