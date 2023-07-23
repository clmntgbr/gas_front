<?php

namespace App\Message;

use App\Entity\EntityId\AddressId;
use App\Entity\EntityId\GasStationId;

final class GeocodingAddressMessage
{
    public function __construct(
        private AddressId $addressId,
        private GasStationId $gasStationId
    ) {
    }

    public function getAddressId(): AddressId
    {
        return $this->addressId;
    }

    public function getGasStationId(): GasStationId
    {
        return $this->gasStationId;
    }
}
