<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Fermier extends User
{
 


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $farmName = null;
    
    #[ORM\Column(type: 'boolean')]
    private $isBanned = false;
    public function getIsBanned(): bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;
        return $this;
    }
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
