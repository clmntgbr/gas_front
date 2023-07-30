<?php

namespace App\MessageHandler;

use App\Entity\Address;
use App\Entity\EntityId\AddressId;
use App\Entity\EntityId\GasStationId;
use App\Entity\GasStation;
use App\Entity\GooglePlace;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGasStationMessage;
use App\Message\GeocodingAddressMessage;
use App\Repository\GasStationBrandRepository;
use App\Repository\GasStationRepository;
use App\Repository\UserRepository;
use App\Service\FileSystemService;
use App\Service\GasServiceService;
use App\Service\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class CreateGasStationMessageHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private readonly MessageBusInterface $messageBus,
        private readonly GasStationRepository $gasStationRepository,
        private readonly GasStationBrandRepository $gasStationBrandRepository,
        private readonly UserRepository $userRepository,
        private readonly GasServiceService $gasServiceService,
    ) {
    }

    public function __invoke(CreateGasStationMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $gasStation = $this->gasStationRepository->findOneBy(['gasStationId' => $message->getGasStationId()->getId()]);

        if ($gasStation instanceof GasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station already exist (gasStationId : %s)', $message->getGasStationId()->getId()));
        }

        if ('' === $message->getLatitude() || '' === $message->getLongitude()) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station longitude/latitude is empty (gasStationId : %s)', $message->getGasStationId()->getId()));
        }

        $user = $this->userRepository->findOneBy(['email' => 'clement@gmail.com']);
        $gasStationBrand = $this->gasStationBrandRepository->findOneBy(['reference' => 'total']);

        $address = new Address();
        $address
            ->setCreatedBy($user)
            ->setUpdatedBy($user)
            ->setCity($message->getCity())
            ->setPostalCode($message->getCp())
            ->setLongitude($message->getLongitude() ? strval(floatval($message->getLongitude()) / 100000) : null)
            ->setLatitude($message->getLatitude() ? strval(floatval($message->getLatitude()) / 100000) : null)
            ->setCountry($message->getCountry())
            ->setStreet($message->getStreet())
            ->setVicinity(sprintf('%s, %s %s, %s', $message->getStreet(), $message->getCp(), $message->getCity(), $message->getCountry()));

        $element = $message->getElement();
        unset($element['prix']);

        $gasStation = new GasStation();
        $gasStation
            ->setCreatedBy($user)
            ->setUpdatedBy($user)
            ->setHasGasStationBrandVerified(false)
            ->setGasStationBrand($gasStationBrand)
            ->setGasStationId($message->getGasStationId()->getId())
            ->setPop($message->getPop())
            ->setElement($element)
            ->setAddress($address)
            ->setGooglePlace(new GooglePlace())
            ->setHash($message->getHash())
            ->setStatus(GasStationStatusReference::CREATED);

        FileSystemService::createDirectoryIfDontExist('public/images/gas_stations');
        $filename = sprintf('%s.jpg', Uuid::v4());
        copy('public/images/gas_stations/64c65583705b7210748642.jpg', sprintf('public/images/gas_stations/%s', $filename));

        $gasStation->getImage()->setName($filename);
        $gasStation->getImage()->setOriginalName($filename);
        $gasStation->getImage()->setDimensions([660, 440]);
        $gasStation->getImage()->setMimeType('jpg');
        $gasStation->getImage()->setSize(86110);

        $this->isGasStationClosed($element, $gasStation);

        if (null !== $gasStation->getClosedAt()) {
            $gasStation->setStatus(GasStationStatusReference::CLOSED);
        }

        $this->gasServiceService->createGasServices($gasStation, $element);

        $this->em->persist($gasStation);
        $this->em->flush();

        // $this->messageBus->dispatch(
        //     new GeocodingAddressMessage(new AddressId($gasStation->getAddress()->getId()), new GasStationId($gasStation->getGasStationId()))
        // );
    }

    /**
     * @param array<mixed> $element
     */
    public function isGasStationClosed(array $element, GasStation $gasStation): void
    {
        if (isset($element['fermeture']['attributes']['type']) && 'D' == $element['fermeture']['attributes']['type']) {
            $gasStation
                ->setClosedAt(\DateTimeImmutable::createFromFormat('Y-m-d H:i:s', str_replace('T', ' ', substr($element['fermeture']['attributes']['debut'], 0, 19))));
        }
    }
}
