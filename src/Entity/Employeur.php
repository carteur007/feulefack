<?php

namespace App\Entity;

use App\Repository\EmployeurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeurRepository::class)]
class Employeur extends Utilisateur
{

    #[ORM\Column(length: 255)]
    private ?string $nomEntreprise = null;

    #[ORM\ManyToMany(targetEntity: PackCV::class, inversedBy: 'employeurs')]
    private Collection $packcvs;


    #[ORM\ManyToMany(targetEntity: Service::class, mappedBy: 'employeurs')]
    private Collection $services;

    #[ORM\ManyToMany(targetEntity: Abonnement::class, inversedBy: 'employeurs')]
    private Collection $abonnements;


    public function __construct()
    {
        parent::__construct();
        $this->packcvs = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->abonnements = new ArrayCollection();
    }


    public function getNomEntreprise(): ?string
    {
        return $this->nomEntreprise;
    }

    public function setNomEntreprise(string $nomEntreprise): static
    {
        $this->nomEntreprise = $nomEntreprise;

        return $this;
    }

    /**
     * @return Collection<int, PackCV>
     */
    public function getPackcvs(): Collection
    {
        return $this->packcvs;
    }

    public function addPackcv(PackCV $packcv): static
    {
        if (!$this->packcvs->contains($packcv)) {
            $this->packcvs->add($packcv);
        }

        return $this;
    }

    public function removePackcv(PackCV $packcv): static
    {
        $this->packcvs->removeElement($packcv);

        return $this;
    }

    /**
     * @return Collection<int, Abonnement>
     */
    public function getAbonnements(): Collection
    {
        return $this->abonnements;
    }

    public function addAbonnement(Abonnement $abonnement): static
    {
        if (!$this->abonnements->contains($abonnement)) {
            $this->abonnements->add($abonnement);
        }

        return $this;
    }

    public function removeAbonnement(Abonnement $abonnement): static
    {
        $this->abonnements->removeElement($abonnement);

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->addEmployeur($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            $service->removeEmployeur($this);
        }

        return $this;
    }


}
