<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Client extends User
{
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Address is required"), Assert\Length(min:3)]
    private ?string $address = null;

   

    

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    

    // Getter et Setter pour l'image

    
    
}