<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Equipements::class, inversedBy: 'devis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipements $equipement = null;

    #[ORM\Column]
    private ?string $proposition = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $fermier = null;

    #[ORM\ManyToOne(inversedBy: 'devis_fournisseur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $fournisseur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEquipement(): ?Equipements
    {
        return $this->equipement;
    }

    public function setEquipement(Equipements $equipement): static
    {
        $this->equipement = $equipement;

        return $this;
    }

    public function getProposition(): ?string
    {
        return $this->proposition;
    }

    public function setProposition(string $proposition): static
    {
        $this->proposition = $proposition;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getFermier(): ?User
    {
        return $this->fermier;
    }

    public function setFermier(?User $fermier): static
    {
        $this->fermier = $fermier;

        return $this;
    }

    public function getFournisseur(): ?User
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?User $fournisseur): static
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

}
