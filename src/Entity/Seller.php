<?php

namespace App\Entity;

use App\Repository\SellerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[ORM\Entity(repositoryClass: SellerRepository::class)]
#[UniqueEntity(fields: ['companyName', 'phoneNumber', 'email'])]
#[Vich\Uploadable]
class Seller
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 50)]
    private ?string $companyName = null;

    #[ORM\Column(length: 13)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 13, max: 13)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank()]
    #[Assert\Email()]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
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

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
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

}
