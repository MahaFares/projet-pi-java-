<?php

namespace App\Entity;

use App\Repository\HebergementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HebergementRepository::class)]
class Hebergement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 100)]
    private ?string $ville = null;

    #[ORM\Column]
    private ?int $nbEtoiles = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $imagePrincipale = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $labelEco = null;

    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\ManyToOne(inversedBy: 'hebergements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategorieHebergement $categorie = null;

    /**
     * @var Collection<int, Equipement>
     */
    #[ORM\ManyToMany(targetEntity: Equipement::class, inversedBy: 'hebergements')]
    private Collection $equipements;

    /**
     * @var Collection<int, Chambre>
     */
    #[ORM\OneToMany(targetEntity: Chambre::class, mappedBy: 'hebergement')]
    private Collection $chambres;

    #[ORM\ManyToOne]
    private ?User $propietaire = null;

    public function __construct()
    {
        $this->equipements = new ArrayCollection();
        $this->chambres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getNbEtoiles(): ?int
    {
        return $this->nbEtoiles;
    }

    public function setNbEtoiles(int $nbEtoiles): static
    {
        $this->nbEtoiles = $nbEtoiles;

        return $this;
    }

    public function getImagePrincipale(): ?string
    {
        return $this->imagePrincipale;
    }

    public function setImagePrincipale(?string $imagePrincipale): static
    {
        $this->imagePrincipale = $imagePrincipale;

        return $this;
    }

    public function getLabelEco(): ?string
    {
        return $this->labelEco;
    }

    public function setLabelEco(?string $labelEco): static
    {
        $this->labelEco = $labelEco;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCategorie(): ?CategorieHebergement
    {
        return $this->categorie;
    }

    public function setCategorie(?CategorieHebergement $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Equipement>
     */
    public function getEquipements(): Collection
    {
        return $this->equipements;
    }

    public function addEquipement(Equipement $equipement): static
    {
        if (!$this->equipements->contains($equipement)) {
            $this->equipements->add($equipement);
        }

        return $this;
    }

    public function removeEquipement(Equipement $equipement): static
    {
        $this->equipements->removeElement($equipement);

        return $this;
    }

    /**
     * @return Collection<int, Chambre>
     */
    public function getChambres(): Collection
    {
        return $this->chambres;
    }

    public function addChambre(Chambre $chambre): static
    {
        if (!$this->chambres->contains($chambre)) {
            $this->chambres->add($chambre);
            $chambre->setHebergement($this);
        }

        return $this;
    }

    public function removeChambre(Chambre $chambre): static
    {
        if ($this->chambres->removeElement($chambre)) {
            // set the owning side to null (unless already changed)
            if ($chambre->getHebergement() === $this) {
                $chambre->setHebergement(null);
            }
        }

        return $this;
    }

    public function getPropietaire(): ?User
    {
        return $this->propietaire;
    }

    public function setPropietaire(?User $propietaire): static
    {
        $this->propietaire = $propietaire;

        return $this;
    }
}
