<?php

namespace App\Service;

use App\Entity\EntityId\AddressId;
use App\Entity\EntityId\GasStationId;
use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceDetailsMessage;
use App\Message\CreateGooglePlaceTextsearchMessage;
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
                GasStationStatusReference::UPDATED_TO_ADDRESS_FORMATED => $this->created($gasStation),

                GasStationStatusReference::ADDRESS_FORMATED => $this->textSearch($gasStation),
                GasStationStatusReference::UPDATED_TO_FOUND_IN_TEXTSEARCH => $this->textSearch($gasStation),

                GasStationStatusReference::FOUND_IN_TEXTSEARCH => $this->textSearch($gasStation),
                GasStationStatusReference::UPDATED_TO_FOUND_IN_DETAILS => $this->textSearch($gasStation),

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

    private function textSearch(GasStation $gasStation)
    {
        $this->messageBus->dispatch(
            new CreateGooglePlaceTextsearchMessage(new GasStationId($gasStation->getGasStationId()))
        );
    }

    private function detailsSearch(GasStation $gasStation)
    {
        $this->messageBus->dispatch(
            new CreateGooglePlaceDetailsMessage(new GasStationId($gasStation->getGasStationId()))
        );
    }
}
