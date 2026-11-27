<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Назва локації обовʼязкова')]
    #[Assert\Length(min: 2, max: 120)]
    private ?string $name = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $zone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Sensor>
     */
    #[ORM\OneToMany(targetEntity: Sensor::class, mappedBy: 'location', orphanRemoval: true)]
    private Collection $sensors;

    public function __construct()
    {
        $this->sensors = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?? 'Нова локація';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getZone(): ?string
    {
        return $this->zone;
    }

    public function setZone(?string $zone): static
    {
        $this->zone = $zone;
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

    /**
     * @return Collection<int, Sensor>
     */
    public function getSensors(): Collection
    {
        return $this->sensors;
    }

    public function addSensor(Sensor $sensor): static
    {
        if (!$this->sensors->contains($sensor)) {
            $this->sensors->add($sensor);
            $sensor->setLocation($this);
        }
        return $this;
    }

    public function removeSensor(Sensor $sensor): static
    {
        if ($this->sensors->removeElement($sensor)) {
            if ($sensor->getLocation() === $this) {
                $sensor->setLocation(null);
            }
        }
        return $this;
    }
}