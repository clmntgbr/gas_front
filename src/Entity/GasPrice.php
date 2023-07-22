<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Repository\GasPriceRepository;
use App\Service\Uuid;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\Blameable;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\Timestampable;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: GasPriceRepository::class)]
#[ApiResource]
class GasPrice
{
    use IdentifyTraits;
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Column(type: Types::INTEGER)]
    private int $value;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y h:i:s'])]
    private DateTimeImmutable $date;

    #[ORM\Column(type: Types::INTEGER)]
    private int $dateTimestamp;

    #[ORM\ManyToOne(targetEntity: GasStation::class, inversedBy: 'gasPrices')]
    #[ORM\JoinColumn(nullable: false)]
    private GasStation $gasStation;

    #[ORM\ManyToOne(targetEntity: GasType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private GasType $gasType;

    #[ORM\ManyToOne(targetEntity: Currency::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Currency $currency;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDateTimestamp(): ?int
    {
        return $this->dateTimestamp;
    }

    public function setDateTimestamp(int $dateTimestamp): static
    {
        $this->dateTimestamp = $dateTimestamp;

        return $this;
    }

    public function getGasStation(): ?GasStation
    {
        return $this->gasStation;
    }

    public function setGasStation(?GasStation $gasStation): static
    {
        $this->gasStation = $gasStation;

        return $this;
    }

    public function getGasType(): ?GasType
    {
        return $this->gasType;
    }

    public function setGasType(?GasType $gasType): static
    {
        $this->gasType = $gasType;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): static
    {
        $this->currency = $currency;

        return $this;
    }
}
