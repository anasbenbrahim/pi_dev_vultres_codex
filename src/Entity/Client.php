<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Client extends User
{
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    public function isEmailConfirmed(): bool
    {
        return $this->confirmationToken === null;
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

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): static
    {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }
}
