<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
#[Vich\Uploadable]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le service est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le service doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le service ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[\p{L}0-9\s]+$/u",
        message: "Le service ne peut contenir que des lettres, des chiffres et des espaces."
    )]
    #[Assert\Type(type: 'string', message: "Le service doit être une chaîne de caractères.")]
    private ?string $service = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotBlank(message: "La date de demande est obligatoire.")]
    #[Assert\Type(\DateTimeInterface::class, message: "La date doit être valide.")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "La date de demande ne peut pas être dans le passé."
    )]
    private ?\DateTimeInterface $date_demande = null;

    #[Vich\UploadableField(mapping: 'cv', fileNameProperty: 'cvFileName')]
    #[Assert\File(
        maxSize: '5M', // Maximum file size (5 MB)
        mimeTypes: ['application/pdf'], // Allowed MIME types
        mimeTypesMessage: 'Veuillez télécharger un fichier PDF valide.'
    )]
    private ?File $cvFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cvFileName = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    #[Assert\NotBlank(message: "Une demande doit être liée à une offre.")]
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

    public function setDateDemande(?\DateTimeInterface $date_demande): static
    {
        $this->date_demande = $date_demande;
        return $this;
    }

    public function getCvFile(): ?File
    {
        return $this->cvFile;
    }

    public function setCvFile(?File $cvFile = null): static
    {
        $this->cvFile = $cvFile;
        if ($cvFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    public function getCvFileName(): ?string
    {
        return $this->cvFileName;
    }

    public function setCvFileName(?string $cvFileName): static
    {
        $this->cvFileName = $cvFileName;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
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
