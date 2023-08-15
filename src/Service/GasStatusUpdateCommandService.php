<?php

namespace App\Service;

use App\Entity\EntityId\AddressId;
use App\Entity\EntityId\GasStationId;
use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use App\Message\GeocodingAddressMessage;
use App\Repository\GasStationRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class GasStatusUpdateCommandService
{
    public function __construct(
        private readonly GasStationRepository $gasStationRepository,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function invoke(): void
    {
        $gasStations = $this->gasStationRepository->findAll();

        foreach ($gasStations as $gasStation) {
            match ($gasStation->getStatus()) {
                GasStationStatusReference::CREATED => $this->created($gasStation),
                default => '',
            };
        }
    }

    private function created(GasStation $gasStation)
    {
        $this->messageBus->dispatch(
            new GeocodingAddressMessage(new AddressId($gasStation->getAddress()->getId()), new GasStationId($gasStation->getGasStationId()))
        );
    }
}
