<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ORM\InheritanceType('JOINED')] // Stratégie d'héritage "JOINED"
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')] // Colonne discriminatrice
#[ORM\DiscriminatorMap([
    'user' => User::class,
    'fermier' => Fermier::class,
    'fournisseur' => Fournisseur::class,
    'employee' => Employee::class,
    'client' => Client::class,
    'superadmin' => Superadmin::class,
])]
abstract class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: "Email is required"), Assert\Email(message: "The email '{ @ }' is not a valid email")]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: "json")]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "LastName is required"), Assert\Length(min:3)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "FirstName is required"), Assert\Length(min:3)]
    private ?string $firstName = null;

    #[ORM\OneToMany(targetEntity: Equipements::class, mappedBy: 'user')]
    private Collection $equipements;

    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'user')]
    private Collection $produits;

    /**
     * @var Collection<int, Devis>
     */
    #[ORM\OneToMany(targetEntity: Devis::class, mappedBy: 'fermier', orphanRemoval: true)]
    private Collection $devis;

    /**
     * @var Collection<int, ReponseDevis>
     */
    #[ORM\OneToMany(targetEntity: ReponseDevis::class, mappedBy: 'fournisseur')]
    private Collection $reponseDevis;

    /**
     * @var Collection<int, Devis>
     */
    #[ORM\OneToMany(targetEntity: Devis::class, mappedBy: 'fournisseur')]
    private Collection $devis_fournisseur;

    public function __construct()
    {
        $this->equipements = new ArrayCollection();
        $this->produits = new ArrayCollection();
        $this->devis = new ArrayCollection();
        $this->reponseDevis = new ArrayCollection();
        $this->devis_fournisseur = new ArrayCollection();
    }

    /**
     * @return Collection<int, Equipements>
     */
    public function getEquipements(): Collection
    {
        return $this->equipements;
    }

    public function addEquipement(Equipements $equipement): static
    {
        if (!$this->equipements->contains($equipement)) {
            $this->equipements->add($equipement);
            $equipement->setUser($this);
        }
        return $this;
    }

    public function removeEquipement(Equipements $equipement): static
    {
        if ($this->equipements->removeElement($equipement)) {
            // set the owning side to null (unless already changed)
            if ($equipement->getUser() === $this) {
                $equipement->setUser(null);
            }
        }
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
            $produit->setUser($this);
        }
        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getUser() === $this) {
                $produit->setUser(null);
            }
        }
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Garantir que chaque utilisateur a au moins ROLE_USER
        return array_unique($roles);
    }

    public function setRoles(array|string $roles): self
    {
        if (is_string($roles)) {
            $roles = json_decode($roles, true) ?? [$roles];
        }

        $validRoles = ['ROLE_SUPER_ADMIN', 'ROLE_CLIENT', 'ROLE_FERMIER', 'ROLE_FOURNISSEUR', 'ROLE_EMPLOYEE'];
        foreach ($roles as $role) {
            if (!in_array($role, $validRoles, true)) {
                throw new \InvalidArgumentException("Rôle invalide : $role");
            }
        }

        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // Effacer les données sensibles temporaires
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return Collection<int, Devis>
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): static
    {
        if (!$this->devis->contains($devi)) {
            $this->devis->add($devi);
            $devi->setFermier($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): static
    {
        if ($this->devis->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getFermier() === $this) {
                $devi->setFermier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ReponseDevis>
     */
    public function getReponseDevis(): Collection
    {
        return $this->reponseDevis;
    }

    public function addReponseDevi(ReponseDevis $reponseDevi): static
    {
        if (!$this->reponseDevis->contains($reponseDevi)) {
            $this->reponseDevis->add($reponseDevi);
            $reponseDevi->setFournisseur($this);
        }

        return $this;
    }

    public function removeReponseDevi(ReponseDevis $reponseDevi): static
    {
        if ($this->reponseDevis->removeElement($reponseDevi)) {
            // set the owning side to null (unless already changed)
            if ($reponseDevi->getFournisseur() === $this) {
                $reponseDevi->setFournisseur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Devis>
     */
    public function getDevisFournisseur(): Collection
    {
        return $this->devis_fournisseur;
    }

    public function addDevisFournisseur(Devis $devisFournisseur): static
    {
        if (!$this->devis_fournisseur->contains($devisFournisseur)) {
            $this->devis_fournisseur->add($devisFournisseur);
            $devisFournisseur->setFournisseur($this);
        }

        return $this;
    }

    public function removeDevisFournisseur(Devis $devisFournisseur): static
    {
        if ($this->devis_fournisseur->removeElement($devisFournisseur)) {
            // set the owning side to null (unless already changed)
            if ($devisFournisseur->getFournisseur() === $this) {
                $devisFournisseur->setFournisseur(null);
            }
        }

        return $this;
    }
}
