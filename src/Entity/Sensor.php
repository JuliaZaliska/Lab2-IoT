<?php

namespace App\Entity;

use App\Repository\SensorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SensorRepository::class)]
class Sensor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Назва датчика обовʼязкова')]
    #[Assert\Length(min: 2, max: 120)]
    private ?string $name = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank(message: 'Вкажіть тип датчика (напр. Температура, Вологість, CO2)')]
    private ?string $sensorType = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Вкажіть поточне значення')]
    private ?float $currentValue = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Вкажіть одиницю виміру (напр. °C, %, ppm, кПа)')]
    private ?string $unit = null;

    #[ORM\Column(nullable: true)]
    private ?float $alertThreshold = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(
        choices: ['online', 'offline', 'maintenance'],
        message: 'Статус має бути: online, offline або maintenance'
    )]
    private string $status = 'online';

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'sensors')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Оберіть локацію')]
    private ?Location $location = null;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->name ?? 'Новий датчик';
    }

    public function isAlert(): bool
    {
        return $this->status === 'online'
            && $this->alertThreshold !== null
            && $this->currentValue !== null
            && $this->currentValue >= $this->alertThreshold;
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

    public function getSensorType(): ?string
    {
        return $this->sensorType;
    }

    public function setSensorType(string $sensorType): static
    {
        $this->sensorType = $sensorType;
        return $this;
    }

    public function getCurrentValue(): ?float
    {
        return $this->currentValue;
    }

    public function setCurrentValue(float $currentValue): static
    {
        $this->currentValue = $currentValue;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): static
    {
        $this->unit = $unit;
        return $this;
    }

    public function getAlertThreshold(): ?float
    {
        return $this->alertThreshold;
    }

    public function setAlertThreshold(?float $alertThreshold): static
    {
        $this->alertThreshold = $alertThreshold;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;
        return $this;
    }
}