<?php

namespace App\Service;

use App\Entity\EntityId\GasStationId;
use App\Entity\GasStation;
use Doctrine\ORM\EntityManagerInterface;

final class GasStationService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function getGasStationId($gasStationId): GasStationId
    {
        if (empty($gasStationId)) {
            throw new \Exception();
        }

        return new GasStationId($gasStationId);
    }

    public function setGasStationStatus(GasStation $gasStation, string $status): GasStation
    {
        $gasStation->setStatus($status);
        $this->em->persist($gasStation);
        $this->em->flush();

        return $gasStation;
    }
}
