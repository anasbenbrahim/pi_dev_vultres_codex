<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Employee extends User
{
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $department = null;

    /**
     * @var Collection<int, Offer>
     */
    #[ORM\ManyToMany(targetEntity: Offer::class, mappedBy: 'employeeoffer')]
    private Collection $offers;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Demande $employeedemande = null;

    public function __construct()
    {
        $this->offers = new ArrayCollection();
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(?string $department): static
    {
        $this->department = $department;

        return $this;
    }

    /**
     * @return Collection<int, Offer>
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): static
    {
        if (!$this->offers->contains($offer)) {
            $this->offers->add($offer);
            $offer->addEmployeeoffer($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): static
    {
        if ($this->offers->removeElement($offer)) {
            $offer->removeEmployeeoffer($this);
        }

        return $this;
    }

    public function getEmployeedemande(): ?Demande
    {
        return $this->employeedemande;
    }

    public function setEmployeedemande(?Demande $employeedemande): static
    {
        $this->employeedemande = $employeedemande;

        return $this;
    }
}