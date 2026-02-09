<?php

namespace App\Entity;

use App\Repository\GuideRepository;
use Doctrine\ORM\Mapping as ORM;
<<<<<<< HEAD
=======
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
>>>>>>> f5ab5f2b8143340c9833c9379b76af33954bf087

#[ORM\Entity(repositoryClass: GuideRepository::class)]
class Guide
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
<<<<<<< HEAD
    private ?string $firstName = null;

    #[ORM\Column(length: 120)]
    private ?string $lastName = null;

    #[ORM\Column(length: 150, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 30)]
    private ?string $phone = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(nullable: true)]
    private ?float $rating = null;

    #[ORM\Column(nullable: true)]
=======
    #[Assert\NotBlank(message: 'Le prénom est requis')]
    #[Assert\Length(min: 2, max: 120, minMessage: 'Le prénom doit contenir au moins 2 caractères', maxMessage: 'Le prénom ne peut pas dépasser 120 caractères')]
    private ?string $firstName = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Le nom de famille est requis')]
    #[Assert\Length(min: 2, max: 120, minMessage: 'Le nom doit contenir au moins 2 caractères', maxMessage: 'Le nom ne peut pas dépasser 120 caractères')]
    private ?string $lastName = null;

    #[ORM\Column(length: 150, unique: true)]
    #[Assert\NotBlank(message: 'L\'adresse email est requise')]
    #[Assert\Email(message: 'L\'adresse email n\'est pas valide')]
    #[Assert\Length(max: 150, maxMessage: 'L\'email ne peut pas dépasser 150 caractères')]
    private ?string $email = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: 'Le numéro de téléphone est requis')]
    #[Assert\Length(min: 10, max: 30, minMessage: 'Le numéro doit contenir au moins 10 chiffres', maxMessage: 'Le numéro ne peut pas dépasser 30 caractères')]
    #[Assert\Regex(pattern: '/^[\d\s\-+()]+$/', message: 'Le numéro de téléphone n\'est pas valide')]
    private ?string $phone = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 5000, maxMessage: 'La biographie ne peut pas dépasser 5000 caractères')]
    private ?string $bio = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: 0, max: 5, notInRangeMessage: 'La note doit être entre 0 et 5')]
    private ?float $rating = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'L\'URL de la photo ne peut pas dépasser 255 caractères')]
>>>>>>> f5ab5f2b8143340c9833c9379b76af33954bf087
    private ?string $photo = null;

    #[ORM\OneToMany(mappedBy: 'guide', targetEntity: Activity::class)]
    private Collection $activities;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(string $v): self { $this->firstName = $v; return $this; }

    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(string $v): self { $this->lastName = $v; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $v): self { $this->email = $v; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(string $v): self { $this->phone = $v; return $this; }

    public function getBio(): ?string { return $this->bio; }
    public function setBio(?string $v): self { $this->bio = $v; return $this; }

    public function getRating(): ?float { return $this->rating; }
    public function setRating(?float $v): self { $this->rating = $v; return $this; }

    public function getPhoto(): ?string { return $this->photo; }
    public function setPhoto(?string $v): self { $this->photo = $v; return $this; }

}
