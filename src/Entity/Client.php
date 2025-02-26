<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\User;

#[ORM\Entity]
class Client extends User
{
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'client')]
    private $reclamations;

    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'client')]
    private $commentaires;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Notification::class)]
    private $notifications;  // This was correctly defined, but now needs initialization

    public function __construct()
    {
        $this->reclamations = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->notifications = new ArrayCollection();  // Initialize notifications collection
    }

    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations[] = $reclamation;
            $reclamation->setClient($this);
        }
        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        if ($this->reclamations->contains($reclamation)) {
            $this->reclamations->removeElement($reclamation);
            if ($reclamation->getClient() === $this) {
                $reclamation->setClient(null);
            }
        }

        return $this;
    }

    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setClient($this);
        }
        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->contains($commentaire)) {
            $this->commentaires->removeElement($commentaire);
            if ($commentaire->getClient() === $this) {
                $commentaire->setClient(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }

    // Getter and Setter for notifications
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setClient($this);
        }
        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            if ($notification->getClient() === $this) {
                $notification->setClient(null);
            }
        }

        return $this;
    }
}
