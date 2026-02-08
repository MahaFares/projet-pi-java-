<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $durationMinutes = null;

    #[ORM\Column(length: 150)]
    private ?string $location = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxParticipants = null;

    #[ORM\Column(nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ActivityCategory $category = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    private ?Guide $guide = null;

    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: ActivitySchedule::class, cascade: ['remove'])]
    private Collection $schedules;

    public function __construct()
    {
        $this->schedules = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $v): self { $this->title = $v; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $v): self { $this->description = $v; return $this; }

    public function getPrice(): ?float { return $this->price; }
    public function setPrice(float $v): self { $this->price = $v; return $this; }

    public function getDurationMinutes(): ?int { return $this->durationMinutes; }
    public function setDurationMinutes(int $v): self { $this->durationMinutes = $v; return $this; }

    public function getLocation(): ?string { return $this->location; }
    public function setLocation(string $v): self { $this->location = $v; return $this; }

    public function getMaxParticipants(): ?int { return $this->maxParticipants; }
    public function setMaxParticipants(?int $v): self { $this->maxParticipants = $v; return $this; }

    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $v): self { $this->image = $v; return $this; }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $v): self { $this->isActive = $v; return $this; }

    public function getCategory(): ?ActivityCategory { return $this->category; }
    public function setCategory(ActivityCategory $c): self { $this->category = $c; return $this; }

    public function getGuide(): ?Guide { return $this->guide; }
    public function setGuide(?Guide $g): self { $this->guide = $g; return $this; }

    public function getSchedules(): Collection { return $this->schedules; }
}
