<?php

namespace App\Entity;

use App\Repository\EquipementsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Mime\Message;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\User;

#[ORM\Entity(repositoryClass: EquipementsRepository::class)]
class Equipements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[Assert\Length(min:4,minMessage:"Veuillez saisir un nom valide"),NotBlank()]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;
    
    #[Assert\PositiveOrZero(message:"veuillez inserer un nombre positif")]
    #[Assert\NotBlank(message:"champs vide veuillez inserer une valeur")]
    #[ORM\Column]
    private ?int $quantite = null;

    #[Assert\NotBlank(message:"champs vide veuillez inserer une valeur")]
    #[Assert\Type(type:"numeric", message:"Le prix doit être un nombre.")]
    #[Assert\Positive(message:"Le prix doit être supérieur à 0.")]
    #[ORM\Column]
    private ?float $prix = null;

    #[Assert\NotBlank(),Length(max:255,maxMessage:"veillez reduire la taille description ")]
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[Assert\Image(
        maxSize: "3M",
        mimeTypes: ["image/jpeg", "image/png", "image/jpg"],
        mimeTypesMessage: "Veuillez insérer une image valide"
    )]    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'equipements')]
    #[ORM\JoinColumn(nullable: true,onDelete:'SET NULL')]
    private ?CategoryEquipements $category = null;


    #[ORM\ManyToOne(targetEntity:User::class,inversedBy: 'equipements')]
    #[ORM\JoinColumn(nullable:false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'equipement', targetEntity: Devis::class)]
    private Collection $devis;
    public function __construct()
    {
        $this->devis = new ArrayCollection();
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

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getCategory(): ?CategoryEquipements
    {
        if($this->category != null)
            return $this->category;
        else
            return null;
    }

    public function setCategory(?CategoryEquipements $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
    public function getDevis(): Collection
    {
        return $this->devis;
    }
    
   
}
