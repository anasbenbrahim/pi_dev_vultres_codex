<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Fermier extends User
{
    #[ORM\Column(length: 255, nullable: true)]
    
    private ?string $farmName = null;

   

    public function getFarmName(): ?string
    {
        return $this->farmName;
    }

    public function setFarmName(?string $farmName): static
    {
        $this->farmName = $farmName;

        return $this;
    }

    
}