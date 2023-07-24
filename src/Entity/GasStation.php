<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\ApiResource\Controller\GasStationsMap;
use App\Entity\Traits\IdentifyTraits;
use App\Repository\GasStationRepository;
use App\Service\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use function Safe\json_encode;

#[ORM\Entity(repositoryClass: GasStationRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get_gas_stations_map' => [
            'method' => 'GET',
            'path' => '/gas_stations/map',
            'controller' => GasStationsMap::class,
            'pagination_enabled' => false,
            'deserialize' => false,
            'read' => false,
            'normalization_context' => ['skip_null_values' => false, 'groups' => ['get_gas_stations', 'common']],
        ],
    ],
    itemOperations: [
        'get' => ['normalization_context' => ['skip_null_values' => false, 'groups' => ['get_gas_station', 'common']]],
    ],
)]
#[Vich\Uploadable]
class GasStation
{
    use IdentifyTraits;
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Groups(['get_gas_stations'])]
    private string $pop;

    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Groups(['get_gas_stations'])]
    private string $gasStationId;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_gas_stations'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_gas_stations'])]
    private ?string $company = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $statuses = [];

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_gas_stations'])]
    private ?string $status;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['get_gas_stations'])]
    private ?\DateTimeImmutable $closedAt = null;

    #[ORM\OneToOne(targetEntity: Address::class, cascade: ['persist', 'remove'])]
    #[Groups(['get_gas_stations'])]
    #[ORM\JoinColumn(nullable: false)]
    private Address $address;

    #[ORM\ManyToOne(targetEntity: GooglePlace::class, cascade: ['persist', 'remove'])]
    #[Groups(['get_gas_stations'])]
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

    #[ORM\OneToMany(mappedBy: 'gasStation', targetEntity: GasPrice::class, cascade: ['persist', 'remove'], fetch: 'LAZY')]
    private Collection $gasPrices;

    #[ORM\ManyToMany(targetEntity: GasService::class, mappedBy: 'gasStations', cascade: ['persist', 'remove'])]
    #[Groups(['get_gas_stations'])]
    private Collection $gasServices;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $lastGasPrices;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $previousGasPrices;

    #[Groups(['get_gas_stations'])]
    private bool $hasLowPrices = false;

    public function __construct()
    {
        $this->statuses = [];
        $this->uuid = Uuid::v4();
        $this->lastGasPrices = [];
        $this->previousGasPrices = [];
        $this->image = new \Vich\UploaderBundle\Entity\File();
        $this->gasPrices = new ArrayCollection();
        $this->gasServices = new ArrayCollection();
    }

    #[Groups(['get_gas_stations'])]
    public function getImagePath(): string
    {
        return sprintf('/images/gas_stations/%s', $this->getImage()->getName());
    }

    #[Groups(['get_gas_stations'])]
    public function getLastPrices(): array
    {
        return array_combine(array_slice([0, 1, 2, 3, 4, 5], 0, count($this->lastGasPrices)), $this->lastGasPrices);
    }

    #[Groups(['get_gas_stations'])]
    public function getPreviousPrices(): array
    {
        return array_combine(array_slice([0, 1, 2, 3, 4, 5], 0, count($this->previousGasPrices)), $this->previousGasPrices);
    }

    public function getImage(): EmbeddedFile
    {
        return $this->image;
    }

    public function setImage(EmbeddedFile $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTime();
        }

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
        if ($this->getPreviousStatus() === $status) {
            return $this;
        }

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

    public function getLastGasPricesAdmin()
    {
        $json = [];
        foreach ($this->lastGasPrices as $key => $gasPrice) {
            $gasPrice['date'] = (new \DateTime('now', new \DateTimeZone('Europe/Paris')))->setTimestamp($gasPrice['gasPriceDatetimestamp'])->format('Y-m-d h:s:i');
            $json[$key] = $gasPrice;
        }

        return json_encode($json, JSON_PRETTY_PRINT);
    }

    public function getPreviousGasPricesAdmin()
    {
        $json = [];
        foreach ($this->previousGasPrices as $key => $gasPrice) {
            $gasPrice['date'] = (new \DateTime())->setTimestamp($gasPrice['gasPriceDatetimestamp'])->format('Y-m-d h:s:i');
            $json[$key] = $gasPrice;
        }

        return json_encode($json, JSON_PRETTY_PRINT);
    }

    public function setClosedAt(?\DateTimeImmutable $closedAt): static
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function isHasLowPrices(): ?bool
    {
        return $this->hasLowPrices;
    }

    public function setHasLowPrices(bool $hasLowPrices): self
    {
        $this->hasLowPrices = $hasLowPrices;

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

    public function getGasStationId(): ?string
    {
        return $this->gasStationId;
    }

    public function setGasStationId(string $gasStationId): static
    {
        $this->gasStationId = $gasStationId;

        return $this;
    }

    /**
     * @return Collection<int, GasPrice>
     */
    public function getGasPrices(): Collection
    {
        return $this->gasPrices;
    }

    public function addGasPrice(GasPrice $gasPrice): static
    {
        if (!$this->gasPrices->contains($gasPrice)) {
            $this->gasPrices->add($gasPrice);
            $gasPrice->setGasStation($this);
        }

        return $this;
    }

    public function removeGasPrice(GasPrice $gasPrice): static
    {
        if ($this->gasPrices->removeElement($gasPrice)) {
            // set the owning side to null (unless already changed)
            if ($gasPrice->getGasStation() === $this) {
                $gasPrice->setGasStation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GasService>
     */
    public function getGasServices(): Collection
    {
        return $this->gasServices;
    }

    public function addGasService(GasService $gasService): static
    {
        if (!$this->gasServices->contains($gasService)) {
            $this->gasServices->add($gasService);
            $gasService->addGasStation($this);
        }

        return $this;
    }

    public function hasGasService(GasService $gasService): bool
    {
        return $this->gasServices->contains($gasService);
    }

    public function getElementAdmin()
    {
        return json_encode($this->element, JSON_PRETTY_PRINT);
    }

    /**
     * @return array<mixed>
     */
    public function getLastGasPrices(): array
    {
        return $this->lastGasPrices;
    }

    public function setLastGasPrices(GasPrice $gasPrice): self
    {
        $value = 'equal';

        if (array_key_exists($gasPrice->getGasType()->getId(), $this->lastGasPrices) && null !== $this->lastGasPrices[$gasPrice->getGasType()->getId()]) {
            $this->previousGasPrices[$gasPrice->getGasType()->getId()] = $this->lastGasPrices[$gasPrice->getGasType()->getId()];
            $value = $this->getGasPriceDifference($gasPrice);
        }

        $this->lastGasPrices[$gasPrice->getGasType()->getId()] = $this->hydrateGasPrices($gasPrice, $value);

        return $this;
    }

    public function addLastGasPrices(array $gasPrice)
    {
        $this->lastGasPrices = $gasPrice;

        return $this;
    }

    private function getGasPriceDifference(GasPrice $gasPrice)
    {
        if ($this->previousGasPrices[$gasPrice->getGasType()->getId()]['gasPriceValue'] > $gasPrice->getValue()) {
            return 'lower';
        }

        if ($this->previousGasPrices[$gasPrice->getGasType()->getId()]['gasPriceValue'] < $gasPrice->getValue()) {
            return 'higher';
        }

        return 'equal';
    }

    /**
     * @return array<mixed>
     */
    public function getPreviousGasPrices()
    {
        return $this->previousGasPrices;
    }

    public function setPreviousGasPrices(GasPrice $gasPrice): self
    {
        $this->previousGasPrices[$gasPrice->getGasType()->getId()] = $this->hydrateGasPrices($gasPrice);

        return $this;
    }

    public function removeGasService(GasService $gasService): self
    {
        if ($this->gasServices->removeElement($gasService)) {
            $gasService->removeGasStation($this);
        }

        return $this;
    }

    private function hydrateGasPrices(GasPrice $gasPrice, string $value = 'equal')
    {
        return [
            'gasPriceId' => $gasPrice->getId(),
            'gasPriceDatetimestamp' => $gasPrice->getDateTimestamp(),
            'gasPriceValue' => $gasPrice->getValue(),
            'gasTypeId' => $gasPrice->getGasType()->getId(),
            'gasTypeLabel' => $gasPrice->getGasType()->getName(),
            'currency' => $gasPrice->getCurrency()->getName(),
            'gasPriceDifference' => $value,
        ];
    }
}
