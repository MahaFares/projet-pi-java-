<?php

namespace App\Entity;

use App\Repository\ChambreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChambreRepository::class)]
class Chambre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $capacite = null;

    #[ORM\Column]
    private ?float $prixParNuit = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $disponible = null;

    #[ORM\ManyToOne(inversedBy: 'chambres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hebergement $hebergement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;

        return $this;
    }

    public function getPrixParNuit(): ?float
    {
        return $this->prixParNuit;
    }

    public function setPrixParNuit(float $prixParNuit): static
    {
        $this->prixParNuit = $prixParNuit;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isDisponible(): ?bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): static
    {
        $this->disponible = $disponible;

        return $this;
    }

    public function getHebergement(): ?Hebergement
    {
        return $this->hebergement;
    }

    public function setHebergement(?Hebergement $hebergement): static
    {
        $this->hebergement = $hebergement;

        return $this;
    }
}
