<?php

namespace App\Service;

use App\Entity\EntityId\GasStationId;

final class GasStationService
{
    public function getGasStationId($gasStationId): GasStationId
    {
        if (empty($gasStationId)) {
            throw new \Exception();
        }

        return new GasStationId($gasStationId);
    }
}
