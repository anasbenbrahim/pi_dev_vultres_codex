<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Reservation
{
    #[ORM\Column(type: "datetime")]

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User")]
    #[ORM\JoinColumn(nullable: false)]
    private $user;



    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;


    #[ORM\ManyToOne(targetEntity: "App\Entity\Event", inversedBy: "reservations")]
    #[ORM\JoinColumn(nullable: false)]
    private $event;


    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    // Add other getters and setters as needed
}
