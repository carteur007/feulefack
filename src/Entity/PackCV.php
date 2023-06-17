<?php

namespace App\Entity;

use App\Repository\PackCVRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PackCVRepository::class)]
class PackCV
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $intitule = null;

    #[ORM\Column]
    private ?int $prix = null;

    #[ORM\ManyToMany(targetEntity: Employeur::class, mappedBy: 'packcvs')]
    private Collection $employeurs;

    public function __construct()
    {
        $this->employeurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): static
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, Employeur>
     */
    public function getEmployeurs(): Collection
    {
        return $this->employeurs;
    }

    public function addEmployeur(Employeur $employeur): static
    {
        if (!$this->employeurs->contains($employeur)) {
            $this->employeurs->add($employeur);
            $employeur->addPackcv($this);
        }

        return $this;
    }

    public function removeEmployeur(Employeur $employeur): static
    {
        if ($this->employeurs->removeElement($employeur)) {
            $employeur->removePackcv($this);
        }

        return $this;
    }

    
}
