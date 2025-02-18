<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Superadmin extends User
{
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Code is required"), Assert\Length(min:3)]
    private ?string $code = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }
}