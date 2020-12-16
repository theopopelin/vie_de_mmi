<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PartieRepository")
 */
class Partie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateFin;

    /**
     * @ORM\Column(type="integer")
     */
    private $quiJoue = 1;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $etatPartie = "NC";

    /**
     * @ORM\Column(type="integer")
     */
    private $gagnant = 0;

    /**
     * @ORM\Column(type="text")
     */
    private $pioche;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $defausse = '';

    /**
     * @ORM\Column(type="float")
     */
    private $cagnotte = 0;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Jouer", mappedBy="partie")
     */
    private $jouers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tchats", mappedBy="partie")
     */
    private $tchats;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Acquerir", mappedBy="partie")
     */
    private $acquerirs;

    public function __construct()
    {
        $this->jouers = new ArrayCollection();
        $this->dateDebut = new DateTime('now');
        $this->tchats = new ArrayCollection();
        $this->Joueur = new ArrayCollection();
        $this->acquerirs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        $idstring = 'aya';
        $idstring = strval($this->getId());
        return $idstring;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getQuiJoue(): ?int
    {
        return $this->quiJoue;
    }

    public function setQuiJoue(int $quiJoue): self
    {
        $this->quiJoue = $quiJoue;

        return $this;
    }

    public function getEtatPartie(): ?string
    {
        return $this->etatPartie;
    }

    public function setEtatPartie(string $etatPartie): self
    {
        $this->etatPartie = $etatPartie;

        return $this;
    }

    public function getGagnant(): ?int
    {
        return $this->gagnant;
    }

    public function setGagnant(int $gagnant): self
    {
        $this->gagnant = $gagnant;

        return $this;
    }

    public function getPioche(): ?array
    {
        return json_decode($this->pioche, true);
    }

    public function setPioche(array $pioche): self
    {
        $this->pioche = json_encode($pioche);

        return $this;
    }

    public function getDefausse(): ?string
    {
        return $this->defausse;
    }

    public function setDefausse(?string $defausse): self
    {
        $this->defausse = $defausse;

        return $this;
    }

    public function getCagnotte(): ?float
    {
        return $this->cagnotte;
    }

    public function setCagnotte(float $cagnotte): self
    {
        $this->cagnotte = $cagnotte;

        return $this;
    }

    /**
     * @return Collection|Jouer[]
     */
    public function getJouers(): Collection
    {
        return $this->jouers;
    }

    public function addJouer(Jouer $jouer): self
    {
        if (!$this->jouers->contains($jouer)) {
            $this->jouers[] = $jouer;
            $jouer->setPartie($this);
        }

        return $this;
    }

    public function removeJouer(Jouer $jouer): self
    {
        if ($this->jouers->contains($jouer)) {
            $this->jouers->removeElement($jouer);
            // set the owning side to null (unless already changed)
            if ($jouer->getPartie() === $this) {
                $jouer->setPartie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tchats[]
     */
    public function getTchats(): Collection
    {
        return $this->tchats;
    }

    public function addTchat(Tchats $tchat): self
    {
        if (!$this->tchats->contains($tchat)) {
            $this->tchats[] = $tchat;
            $tchat->setPartie($this);
        }

        return $this;
    }

    public function removeTchat(Tchats $tchat): self
    {
        if ($this->tchats->contains($tchat)) {
            $this->tchats->removeElement($tchat);
            // set the owning side to null (unless already changed)
            if ($tchat->getPartie() === $this) {
                $tchat->setPartie(null);
            }
        }

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
            $acquerir->setPartie($this);
        }

        return $this;
    }

    public function removeAcquerir(Acquerir $acquerir): self
    {
        if ($this->acquerirs->contains($acquerir)) {
            $this->acquerirs->removeElement($acquerir);
            // set the owning side to null (unless already changed)
            if ($acquerir->getPartie() === $this) {
                $acquerir->setPartie(null);
            }
        }

        return $this;
    }
    
}
