<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le service est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom du service ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $service = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de demande est obligatoire.")]
    #[Assert\Type(\DateTimeInterface::class, message: "La date doit être valide.")]
    private ?\DateTimeInterface $date_demande = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le CV est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom du fichier CV ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $CV = null;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    #[Assert\NotNull(message: "Une demande doit être liée à une offre.")]
    private ?Offer $offer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(string $service): static
    {
        $this->service = $service;
        return $this;
    }

    public function getDateDemande(): ?\DateTimeInterface
    {
        return $this->date_demande;
    }

    public function setDateDemande(\DateTimeInterface $date_demande): static
    {
        $this->date_demande = $date_demande;
        return $this;
    }

    public function getCV(): ?string
    {
        return $this->CV;
    }

    public function setCV(string $CV): static
    {
        $this->CV = $CV;
        return $this;
    }

    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    public function setOffer(?Offer $offer): static
    {
        $this->offer = $offer;
        return $this;
    }
}
