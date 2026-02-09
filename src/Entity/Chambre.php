<?php

namespace App\Entity;

use App\Repository\ChambreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChambreRepository::class)]
class Chambre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le numéro de la chambre ne peut pas être vide.")]
    #[Assert\Length(min: 1, max: 50, maxMessage: "Le numéro est trop long ({{ limit }} caractères maximum).")]
    private ?string $numero = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le type de la chambre ne peut pas être vide.")]
    #[Assert\Length(min: 2, max: 50, minMessage: "Le type doit contenir au moins {{ limit }} caractères.")]
    private ?string $type = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La capacité doit être renseignée.")]
    #[Assert\Positive(message: "La capacité doit être un entier positif.")]
    private ?int $capacite = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le prix par nuit doit être renseigné.")]
    #[Assert\Positive(message: "Le prix par nuit doit être un nombre positif.")]
    private ?float $prixParNuit = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 2000, maxMessage: "La description est trop longue ({{ limit }} caractères maximum).")]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La disponibilité doit être renseignée.")]
    #[Assert\Type(type: 'bool', message: "La disponibilité doit être un booléen.")]
    private ?bool $disponible = null;

    #[ORM\ManyToOne(inversedBy: 'chambres')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'hébergement associé doit être renseigné.")]
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
