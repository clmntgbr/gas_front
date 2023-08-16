<?php

namespace App\MessageHandler;

use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceAnomalyMessage;
use App\Repository\GasStationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

#[AsMessageHandler()]
final class CreateGooglePlaceAnomalyMessageHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private GasStationRepository $gasStationRepository
    ) {
    }

    public function __invoke(CreateGooglePlaceAnomalyMessage $message): void
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $gasStation = $this->gasStationRepository->findOneBy(['gasStationId' => $message->getGasStationId()->getId()]);

        if (null === $gasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station doesnt exist (id : %s)', $message->getGasStationId()->getId()));
        }

        if (GasStationStatusReference::PLACE_ID_ANOMALY === $gasStation->getStatus()) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station has already PLACE_ID_ANOMALY status (id : %s)', $message->getGasStationId()->getId()));
        }

        $gasStation->setStatus(GasStationStatusReference::PLACE_ID_ANOMALY);

        $this->em->persist($gasStation);
        $this->em->flush();
    }
}
