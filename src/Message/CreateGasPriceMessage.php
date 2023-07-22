<?php

namespace App\Message;

use App\Entity\EntityId\GasStationId;
use App\Entity\EntityId\GasTypeId;

final class CreateGasPriceMessage
{
    public function __construct(
        private readonly GasStationId $gasStationId,
        private readonly GasTypeId $gasTypeId,
        private readonly ?string $date,
        private readonly ?string $value
    ) {
    }

    public function getGasStationId(): GasStationId
    {
        return $this->gasStationId;
    }

    public function getGasTypeId(): GasTypeId
    {
        return $this->gasTypeId;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
