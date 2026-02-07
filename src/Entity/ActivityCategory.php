<?php

namespace App\Entity;

use App\Repository\ActivityCategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ActivityCategoryRepository::class)]
class ActivityCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
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


