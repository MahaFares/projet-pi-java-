<?php

namespace App\Entity;

use App\Repository\ActivityCategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
<<<<<<< HEAD

#[ORM\Entity(repositoryClass: ActivityCategoryRepository::class)]
=======
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ActivityCategoryRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Une catégorie avec ce nom existe déjà')]
>>>>>>> f5ab5f2b8143340c9833c9379b76af33954bf087
class ActivityCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
<<<<<<< HEAD
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
=======
    #[Assert\NotBlank(message: 'Le nom de la catégorie est requis')]
    #[Assert\Length(min: 3, max: 100, minMessage: 'Le nom doit contenir au moins 3 caractères', maxMessage: 'Le nom ne peut pas dépasser 100 caractères')]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 1000, maxMessage: 'La description ne peut pas dépasser 1000 caractères')]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'L\'icone ne peut pas dépasser 255 caractères')]
>>>>>>> f5ab5f2b8143340c9833c9379b76af33954bf087
    private ?string $icon = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Activity::class)]
    private Collection $activities;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $d): self { $this->description = $d; return $this; }

    public function getIcon(): ?string { return $this->icon; }
    public function setIcon(?string $i): self { $this->icon = $i; return $this; }

    public function getActivities(): Collection { return $this->activities; }
}


