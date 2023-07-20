<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Repository\GasStationRepository;
use App\Service\Uuid;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\Blameable;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: GasStationRepository::class)]
#[ApiResource]
class GasStation
{
    use IdentifyTraits;
    use Timestampable;
    use Blameable;

    #[ORM\Column(type: Types::STRING, length: 10)]
    private string $pop;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $company = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $statuses;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $status;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $closedAt = null;

    #[ORM\OneToOne(targetEntity: Address::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Address $address;

    #[ORM\ManyToOne(targetEntity: GooglePlace::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private GooglePlace $googlePlace;

    #[ORM\Column(type: Types::JSON)]
    private array $element = [];

    #[Vich\UploadableField(mapping: 'gas_stations_image', fileNameProperty: 'image.name', size: 'image.size', mimeType: 'image.mimeType', originalName: 'image.originalName', dimensions: 'image.dimensions')]
    private ?File $imageFile = null;

    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $image;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $hash;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->image = new \Vich\UploaderBundle\Entity\File();
    }

    public function getImage(): \Vich\UploaderBundle\Entity\File
    {
        return $this->image;
    }

    public function setImage(\Vich\UploaderBundle\Entity\File $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getPop(): ?string
    {
        return $this->pop;
    }

    public function setPop(string $pop): static
    {
        $this->pop = $pop;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        $this->setStatuses($status);

        return $this;
    }

    public function getStatuses(): ?array
    {
        return $this->statuses;
    }

    public function setStatuses(string $status): self
    {
        $this->statuses[] = $status;

        return $this;
    }

    public function setInitStatuses(array $status): self
    {
        $this->statuses = $status;

        return $this;
    }

    public function getPreviousStatus(): ?string
    {
        if (count($this->statuses) <= 1) {
            return end($this->statuses);
        }

        return $this->statuses[count($this->statuses) - 2];
    }

    public function getClosedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeImmutable $closedAt): static
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getElement(): array
    {
        return $this->element;
    }

    public function setElement(array $element): static
    {
        $this->element = $element;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getGooglePlace(): ?GooglePlace
    {
        return $this->googlePlace;
    }

    public function setGooglePlace(?GooglePlace $googlePlace): static
    {
        $this->googlePlace = $googlePlace;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): static
    {
        $this->hash = $hash;

        return $this;
    }
}
