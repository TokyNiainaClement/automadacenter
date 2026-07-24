<?php

namespace App\Entity;

use App\Repository\SellerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[ORM\Entity(repositoryClass: SellerRepository::class)]
#[UniqueEntity(
    fields: ['companyName'],
    message: 'Cette entreprise existe déjà.'
)]
#[UniqueEntity(
    fields: ['email'],
    message: 'Cette adresse email est déjà utilisée.'
)]
#[Vich\Uploadable]
class Seller
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $companyName = null;

    #[ORM\Column(length: 20)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'verification_documents', fileNameProperty: 'verificationDocument')]
    private ?File $documentFile = null;

    // NOTE: This field and the next one need to be nullable, otherwise the deletion won't work
    //       if you want non-nullable fields, set the "erase_fields" option to false in the mapping config
    #[ORM\Column(length: 255)]
    private ?string $verificationDocument = null;

    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'logo_images', fileNameProperty: 'logoName')]
    private ?File $logoFile = null;

    #[ORM\Column(length: 255)]
    private ?string $logoName = null;

    #[ORM\Column(length: 10)]
    private ?string $status = 'pending';

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(inversedBy: 'seller', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, AdminNotification>
     */
    #[ORM\OneToMany(targetEntity: AdminNotification::class, mappedBy: 'seller', orphanRemoval: true)]
    private Collection $adminNotifications;

    /**
     * @var Collection<int, Vehicle>
     */
    #[ORM\OneToMany(targetEntity: Vehicle::class, mappedBy: 'seller', orphanRemoval: true)]
    private Collection $vehicles;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
        $this->adminNotifications = new ArrayCollection();
        $this->vehicles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param File|null $documentFile
     * @return void
     */
    public function setDocumentFile(?File $documentFile = null): void
    {
        $this->documentFile = $documentFile;

        if (null !== $documentFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * Get the value of documentFile
     */ 
    public function getDocumentFile(): ?File
    {
        return $this->documentFile;
    }
    
    public function setVerificationDocument(?string $verificationDocument): static
    {
        $this->verificationDocument = $verificationDocument;

        return $this;
    }

    public function getVerificationDocument(): ?string
    {
        return $this->verificationDocument;
    }


    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    public function setLogoFile(?File $logoFile): void
    {
        $this->logoFile = $logoFile;

        if (null !== $logoFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * Set the value of logoName
     *
     * @return  self
     */ 
    public function setLogoName(?string $logoName)
    {
        $this->logoName = $logoName;

        return $this;
    }

    /**
     * Get the value of logoName
     */ 
    public function getLogoName(): ?string
    {
        return $this->logoName;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, AdminNotification>
     */
    public function getAdminNotifications(): Collection
    {
        return $this->adminNotifications;
    }

    public function addAdminNotification(AdminNotification $adminNotification): static
    {
        if (!$this->adminNotifications->contains($adminNotification)) {
            $this->adminNotifications->add($adminNotification);
            $adminNotification->setSeller($this);
        }

        return $this;
    }

    public function removeAdminNotification(AdminNotification $adminNotification): static
    {
        if ($this->adminNotifications->removeElement($adminNotification)) {
            // set the owning side to null (unless already changed)
            if ($adminNotification->getSeller() === $this) {
                $adminNotification->setSeller(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Vehicle>
     */
    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function addVehicle(Vehicle $vehicle): static
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles->add($vehicle);
            $vehicle->setSeller($this);
        }

        return $this;
    }

    public function removeVehicle(Vehicle $vehicle): static
    {
        if ($this->vehicles->removeElement($vehicle)) {
            // set the owning side to null (unless already changed)
            if ($vehicle->getSeller() === $this) {
                $vehicle->setSeller(null);
            }
        }

        return $this;
    }

}
