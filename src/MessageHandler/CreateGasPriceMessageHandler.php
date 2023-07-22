<?php

namespace App\MessageHandler;

use App\Entity\GasPrice;
use App\Entity\GasStation;
use App\Entity\GasType;
use App\Lists\CurrencyReference;
use App\Message\CreateGasPriceMessage;
use App\Repository\CurrencyRepository;
use App\Repository\GasPriceRepository;
use App\Repository\GasStationRepository;
use App\Repository\GasTypeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Safe\DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

#[AsMessageHandler()]
final class CreateGasPriceMessageHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private readonly GasStationRepository $gasStationRepository,
        private readonly GasTypeRepository $gasTypeRepository,
        private readonly GasPriceRepository $gasPriceRepository,
        private readonly CurrencyRepository $currencyRepository
    ) {
    }

    public function __invoke(CreateGasPriceMessage $message): void
    {
        // if (!$this->em->isOpen()) {
        //     $this->em = EntityManager::create($this->em->getConnection(), $this->em->getConfiguration());
        // }

        // $gasStation = $this->gasStationRepository->findOneBy(['id' => $message->getGasStationId()->getId()]);

        // if (null === $gasStation) {
        //     throw new UnrecoverableMessageHandlingException(sprintf('Gas Station is null (id: %s)', $message->getGasStationId()->getId()));
        // }

        // $gasType = $this->gasTypeRepository->findOneBy(['id' => $message->getGasTypeId()->getId()]);

        // if (null === $gasType) {
        //     throw new UnrecoverableMessageHandlingException(sprintf('Gas Type is null (id: %s, GasStationId: %s)', $message->getGasTypeId()->getId(), $message->getGasStationId()->getId()));
        // }

        // $currency = $this->currencyRepository->findOneBy(['reference' => CurrencyReference::EUR]);

        // if (null === $currency) {
        //     throw new UnrecoverableMessageHandlingException('Currency is null (reference: eur)');
        // }

        // if (null == $message->getValue()) {
        //     throw new UnrecoverableMessageHandlingException(sprintf('Value is null (id: %s)', $message->getValue()));
        // }

        // if (null == $message->getDate()) {
        //     throw new UnrecoverableMessageHandlingException(sprintf('Date is null (id: %s)', $message->getDate()));
        // }

        // $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', str_replace('T', ' ', substr($message->getDate(), 0, 19)));

        // $gasPrice = $this->gasPriceRepository->findOneBy(['dateTimestamp' => $date->getTimestamp(), 'gasType' => $gasType, 'gasStation' => $gasStation]);
        // if ($gasPrice instanceof GasPrice) {
        //     return;
        // }

        // $gasPriceValue = (int) str_replace([',', '.'], '', $message->getValue());
        // $lastGasPrices = $gasStation->getLastGasPrices();

        // if (!$this->hasToBeCreated($lastGasPrices, $gasPriceValue, $gasType, $date)) {
        //     return;
        // }

        // $gasPrice = new GasPrice();
        // $gasPrice
        //     ->setCurrency($currency)
        //     ->setGasType($gasType)
        //     ->setGasStation($gasStation)
        //     ->setDate($date)
        //     ->setDateTimestamp($date->getTimestamp())
        //     ->setValue($gasPriceValue);

        // $this->em->persist($gasPrice);
        // $this->em->flush();

        // $this->updateLastGasPrices($gasStation, $gasPrice, $gasType);

        // $this->em->persist($gasStation);
        // $this->em->flush();
    }

    // private function hasToBeCreated(array $lastGasPrices, int $gasPriceValue, GasType $gasType, DateTimeImmutable $date): bool
    // {
    //     if (!array_key_exists($gasType->getId(), $lastGasPrices)) {
    //         return true;
    //     }

    //     $lastGasPrice = $lastGasPrices[$gasType->getId()];
    //     $lastGasPriceDate = (new \DateTime())->setTimestamp($lastGasPrice['datetimestamp']);

    //     if ($lastGasPrice['gasPriceValue'] !== $gasPriceValue && $date->getTimestamp() > $lastGasPriceDate->getTimestamp()) {
    //         return true;
    //     }

    //     if ($date->format('W') <= $lastGasPriceDate->format('W')) {
    //         return false;
    //     }

    //     if ($date->format('W') > $lastGasPriceDate->format('W')) {
    //         return true;
    //     }

    //     return false;
    // }

    // private function updateLastGasPrices(GasStation $gasStation, GasPrice $gasPrice, GasType $gasType): void
    // {
    //     $gasStation->setLastGasPrices($gasPrice);
    //     $this->updateGasStationIsClosed($gasStation);
    // }

    // private function updateGasStationIsClosed(GasStation $gasStation)
    // {
    //     if (null === $gasStation->getClosedAt()) {
    //         return;
    //     }

    //     $gasStation->setClosedAt(null);
    //     $gasStation->setStatus($gasStation->getPreviousStatus());
    // }
}
