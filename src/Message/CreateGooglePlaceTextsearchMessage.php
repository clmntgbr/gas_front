<?php

namespace App\Message;

use App\Entity\EntityId\GasStationId;

final class CreateGooglePlaceTextsearchMessage
{
    public function __construct(private readonly GasStationId $gasStationId)
    {
    }

    public function getGasStationId(): GasStationId
    {
        return $this->gasStationId;
    }
}
