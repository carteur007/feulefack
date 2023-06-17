<?php

namespace App\Entity;

use App\Repository\CandidatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatRepository::class)]
class Candidat extends Utilisateur
{

    #[ORM\Column(length: 5)]
    private ?string $genre = null;

    #[ORM\ManyToMany(targetEntity: OffreEmploi::class, inversedBy: 'candidats')]
    private Collection $offreEmplois;

    #[ORM\OneToOne(targetEntity: CV::class, mappedBy: 'candidat', cascade: ['persist', 'remove'])]
    private ?CV $userCV = null;

    #[ORM\ManyToMany(targetEntity: Formation::class, mappedBy: 'candidats')]
    private Collection $formations;

    public function __construct()
    {
        parent::__construct();
        $this->offreEmplois = new ArrayCollection();
        $this->formations = new ArrayCollection();
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Collection<int, OffreEmploi>
     */
    public function getOffreEmplois(): Collection
    {
        return $this->offreEmplois;
    }

    public function addOffreEmploi(OffreEmploi $offreEmploi): static
    {
        if (!$this->offreEmplois->contains($offreEmploi)) {
            $this->offreEmplois->add($offreEmploi);
        }

        return $this;
    }

    public function removeOffreEmploi(OffreEmploi $offreEmploi): static
    {
        $this->offreEmplois->removeElement($offreEmploi);

        return $this;
    }

    public function getUserCV(): ?CV
    {
        return $this->userCV;
    }

    public function setUserCV(CV $userCV): static
    {
        // set the owning side of the relation if necessary
        if ($userCV->getCandidat() !== $this) {
            $userCV->setCandidat($this);
        }

        $this->userCV = $userCV;

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->addCandidat($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): static
    {
        if ($this->formations->removeElement($formation)) {
            $formation->removeCandidat($this);
        }

        return $this;
    }
}
