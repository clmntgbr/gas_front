<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Repository\AddressRepository;
use App\Service\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\Blameable;
use Gedmo\Timestampable\Traits\Timestampable;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ApiResource]
class Address
{
    use IdentifyTraits;
    use Timestampable;
    use Blameable;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $vicinity = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $street;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $number = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $city;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $region = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $postalCode;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $country;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $longitude;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $latitude;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }

    public function getVicinity(): ?string
    {
        return $this->vicinity;
    }

    public function setVicinity(?string $vicinity): static
    {
        $this->vicinity = $vicinity;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }
}