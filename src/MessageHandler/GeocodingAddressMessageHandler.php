<?php

namespace App\MessageHandler;

use App\Common\EntityId\GasStationId;
use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceTextsearchMessage;
use App\Message\ErrorFormatAddressMessage;
use App\Message\ErrorGeocodingAddressMessage;
use App\Message\FormatAddressMessage;
use App\Message\GeocodingAddressMessage;
use App\Repository\AddressRepository;
use App\Repository\GasStationRepository;
use App\Service\AddressService;
use App\Service\GasStationService;
use App\Service\PositionStackApiService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler()]
final class GeocodingAddressMessageHandler
{
    public const CONFIDENCE_ERROR = 0.8;

    public function __construct(
        private EntityManagerInterface $em,
        private readonly AddressRepository $addressRepository,
        private readonly PositionStackApiService $positionStackApiService,
        private readonly MessageBusInterface $messageBus,
        private readonly AddressService $addressService,
        private readonly GasStationService $gasStationService,
        private readonly GasStationRepository $gasStationRepository
    ) {
    }

    public function __invoke(GeocodingAddressMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $address = $this->addressRepository->findOneBy(['id' => $message->getAddressId()->getId()]);

        if (null === $address) {
            throw new UnrecoverableMessageHandlingException(sprintf('Address is null (id: %s)', $message->getAddressId()->getId()));
        }

        $data = $this->positionStackApiService->forward($address);

        if (null === $data) {
            return $this->messageBus->dispatch(new ErrorGeocodingAddressMessage($message->getAddressId(), $message->getGasStationId()));
        }

        $address->setPositionStackApiResult($data);

        if (!array_key_exists('confidence', $data)) {
            return $this->messageBus->dispatch(new ErrorGeocodingAddressMessage($message->getAddressId(), $message->getGasStationId()));
        }

        if ($data['confidence'] < self::CONFIDENCE_ERROR) {
            return $this->messageBus->dispatch(new ErrorGeocodingAddressMessage($message->getAddressId(), $message->getGasStationId()));
        }

        $this->addressService->hydrate($address, $data);

        $gasStation = $this->gasStationRepository->findOneBy(['gasStationId' => $message->getGasStationId()->getId()]);

        if (!$gasStation instanceof GasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station doesn\'t exist (gasStationId : %s)', $message->getGasStationId()->getId()));
        }

        $this->gasStationService->setGasStationStatus($gasStation, GasStationStatusReference::ADDRESS_FORMATED);

        // return $this->messageBus->dispatch(new CreateGooglePlaceTextsearchMessage($message->getGasStationId()));
    }
}
