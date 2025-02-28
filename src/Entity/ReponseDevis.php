<?php

namespace App\Entity;

use App\Repository\ReponseDevisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponseDevisRepository::class)]
class ReponseDevis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reponse = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Devis $devis = null;

    #[ORM\ManyToOne(inversedBy: 'reponseDevis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $fournisseur = null;

    #[ORM\ManyToOne(inversedBy: 'fermier_reponsedevis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $fermier = null;

    #[ORM\Column]
    private ?bool $etat = null;

    #[ORM\Column]
    private ?float $prix = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(string $reponse): static
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(Devis $devis): static
    {
        $this->devis = $devis;

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

    public function getFermier(): ?User
    {
        return $this->fermier;
    }

    public function setFermier(?User $fermier): static
    {
        $this->fermier = $fermier;

        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): static
    {
        $this->etat = $etat;

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
}
