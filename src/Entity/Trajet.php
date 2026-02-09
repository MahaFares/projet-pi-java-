<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\TrajetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrajetRepository::class)]
class Trajet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
#[Assert\NotBlank(message: "Le lieu de départ est obligatoire")]
#[Assert\Length(
    min: 2,
    minMessage: "Le lieu de départ doit contenir au moins {{ limit }} caractères"
)]
private ?string $depart = null;
    #[ORM\Column(length: 255)]
#[Assert\NotBlank(message: "Le lieu d'arrivée est obligatoire")]
#[Assert\Length(
    min: 2,
    minMessage: "Le lieu d'arrivée doit contenir au moins {{ limit }} caractères"
)]
private ?string $arrivee = null;

   #[ORM\Column]
#[Assert\NotBlank(message: "La date de départ est obligatoire")]
#[Assert\GreaterThan(
    value: "today",
    message: "La date de départ doit être dans le futur"
)]
private ?\DateTimeInterface $dateDepart = null;

    #[ORM\Column]
#[Assert\NotBlank(message: "La distance est obligatoire")]
#[Assert\Positive(message: "La distance doit être positive")]
#[Assert\Range(
    min: 1,
    max: 1000,
    notInRangeMessage: "La distance doit être entre {{ min }} et {{ max }} km"
)]
private ?float $distanceKm = null;

    #[ORM\Column]
#[Assert\NotBlank(message: "Le nombre de places est obligatoire")]
#[Assert\PositiveOrZero(message: "Le nombre de places doit être positif ou zéro")]
private ?int $placesDisponibles = null;

    #[ORM\ManyToOne(inversedBy: 'trajets')]
#[ORM\JoinColumn(nullable: false)]
#[Assert\NotNull(message: "Vous devez sélectionner un transport")]
private ?Transport $transport = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepart(): ?string
    {
        return $this->depart;
    }

    public function setDepart(string $depart): static
    {
        $this->depart = $depart;

        return $this;
    }

    public function getArrivee(): ?string
    {
        return $this->arrivee;
    }

    public function setArrivee(string $arrivee): static
    {
        $this->arrivee = $arrivee;

        return $this;
    }

    public function getDateDepart(): ?\DateTime
    {
        return $this->dateDepart;
    }

    public function setDateDepart(\DateTime $dateDepart): static
    {
        $this->dateDepart = $dateDepart;

        return $this;
    }

    public function getDistancekm(): ?float
    {
        return $this->distancekm;
    }

    public function setDistancekm(float $distancekm): static
    {
        $this->distancekm = $distancekm;

        return $this;
    }

    public function getPlaceDisponible(): ?int
    {
        return $this->placeDisponible;
    }

    public function setPlaceDisponible(int $placeDisponible): static
    {
        $this->placeDisponible = $placeDisponible;

        return $this;
    }

    public function getTransport(): ?transport
    {
        return $this->transport;
    }

    public function setTransport(?transport $transport): static
    {
        $this->transport = $transport;

        return $this;
    }
}
