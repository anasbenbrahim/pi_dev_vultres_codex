<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;

use App\Repository\MarcheRepository;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: MarcheRepository::class)]
class Marche
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idUser = null;


    #[ORM\Column]
    private ?int $prodid = null;


    #[Assert\NotBlank(message: 'veuillez remplir ce champ')]
    #[Assert\Length(min: 3, minMessage: 'Le libelle doit comporter au moins {{ limit }} caractères')]
    #[ORM\Column(length: 150)]
    private ?string $nomprod = null;


    #[Assert\NotBlank(message: 'Le prix du produit ne peut pas être vides.')]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: 'Le prix du produit ne peut pas être négatifs.'
    )]
    #[ORM\Column]
    private ?float $prix = null;


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateajout = null;


    #[Assert\NotBlank(message: 'La quantite ne peut pas être vides.')]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: 'La quantite ne peut pas être négatifs.'
    )]
    #[ORM\Column]
    private ?float $quantite = null;

    #[ORM\Column(length: 150)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'marche', targetEntity: Produit::class)]
    private Collection $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getProdid(): ?int
    {
        return $this->prodid;
    }

    public function setProdid(int $prodid): static
    {
        $this->prodid = $prodid;

        return $this;
    }

    public function getNomprod(): ?string
    {
        return $this->nomprod;
    }

    public function setNomprod(string $nomprod): static
    {
        $this->nomprod = $nomprod;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDateajout(): ?\DateTimeInterface
    {
        return $this->dateajout;
    }

    public function setDateajout(\DateTimeInterface $dateajout): static
    {
        $this->dateajout = $dateajout;

        return $this;
    }

    public function getQuantite(): ?float
    {
        return $this->qte;
    }

    public function setQuantite(float $qte): static
    {
        $this->qte = $qte;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->setMarche($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getMarche() === $this) {
                $produit->setMarche(null);
            }
        }

        return $this;
    }
}
