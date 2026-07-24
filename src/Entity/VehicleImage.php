<?php

namespace App\Entity;

use App\Repository\VehicleImageRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[ORM\Entity(repositoryClass: VehicleImageRepository::class)]
#[Vich\Uploadable]
class VehicleImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'vehicle_images', fileNameProperty: 'vehicleImageName')]
    private ?File $vehicleImageFile = null;

    #[ORM\Column(length: 255)]
    private ?string $vehicleImageName = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'vehicleImages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicle $vehicle = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of vehicleImageFile
     */ 
    public function getVehicleImageFile(): ?File
    {
        return $this->vehicleImageFile;
    }

    /**
     * Set the value of vehicleImageFile
     *
     * @return  void
     */ 
    public function setVehicleImageFile(?File $vehicleImageFile): void
    {
        $this->vehicleImageFile = $vehicleImageFile;

        if (null !== $vehicleImageFile) {
            $this->createdAt = new DateTimeImmutable();
        }

    }

    /**
     * Get the value of vehicleImageName
     */ 
    public function getVehicleImageName(): ?string
    {
        return $this->vehicleImageName;
    }

    /**
     * Set the value of vehicleImageName
     *
     * @return  self
     */ 
    public function setVehicleImageName(?string $vehicleImageName)
    {
        $this->vehicleImageName = $vehicleImageName;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): static
    {
        $this->vehicle = $vehicle;

        return $this;
    }

}
