<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Fournisseur extends User
{
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Company Name is required"), Assert\Length(min:3)]
    private ?string $companyName = null;

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }
}