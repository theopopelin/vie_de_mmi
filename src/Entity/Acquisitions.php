<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AcquisitionsRepository")
 */
class Acquisitions
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $coutachat;

    /**
     * @ORM\Column(type="integer")
     */
    private $prixrevente;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $effet;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Acquerir", mappedBy="Acquisition")
     */
    private $acquerirs;

    public function __construct()
    {
        $this->acquerirs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCoutachat(): ?int
    {
        return $this->coutachat;
    }

    public function setCoutachat(int $coutachat): self
    {
        $this->coutachat = $coutachat;

        return $this;
    }

    public function getPrixrevente(): ?int
    {
        return $this->prixrevente;
    }

    public function setPrixrevente(int $prixrevente): self
    {
        $this->prixrevente = $prixrevente;

        return $this;
    }

    public function getEffet(): ?string
    {
        return $this->effet;
    }

    public function setEffet(string $effet): self
    {
        $this->effet = $effet;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Acquerir[]
     */
    public function getAcquerirs(): Collection
    {
        return $this->acquerirs;
    }

    public function addAcquerir(Acquerir $acquerir): self
    {
        if (!$this->acquerirs->contains($acquerir)) {
            $this->acquerirs[] = $acquerir;
            $acquerir->setAcquisition($this);
        }

        return $this;
    }

    public function removeAcquerir(Acquerir $acquerir): self
    {
        if ($this->acquerirs->contains($acquerir)) {
            $this->acquerirs->removeElement($acquerir);
            // set the owning side to null (unless already changed)
            if ($acquerir->getAcquisition() === $this) {
                $acquerir->setAcquisition(null);
            }
        }

        return $this;
    }
}
