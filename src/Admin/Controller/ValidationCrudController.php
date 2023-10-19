<?php

namespace App\Admin\Controller;

use App\Entity\EntityId\GasStationId;
use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceTextsearchMessage;
use App\Repository\GasStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ValidationCrudController extends AbstractController
{
    #[Route('/admin/validation', name: 'app_admin_validation')]
    public function index(EntityManagerInterface $em, GasStationRepository $gasStationRepository): Response
    {
        $gasStation = $gasStationRepository->findRandomGasStation();
        $gasStations = $gasStationRepository->getGasStationGooglePlaceByPlaceId($gasStation);

        return $this->render('Admin/validation.html.twig', [
            'gasStation' => $gasStation,
            'gasStations' => $gasStations,
        ]);
    }

    #[Route('/admin/validation/validate/{gasStationId}', name: 'app_admin_validation_validate')]
    public function validate(EntityManagerInterface $em, GasStation $gasStation): Response
    {
        if ($gasStation->getStatus() !== GasStationStatusReference::WAITING_VALIDATION) {
            return $this->redirect('/admin?routeName=app_admin_validation');
        }

        $gasStation->setStatus(GasStationStatusReference::VALIDATED);
        $gasStation->setStatus(GasStationStatusReference::OPEN);

        $em->persist($gasStation);
        $em->flush();

        return $this->redirect('/admin?routeName=app_admin_validation');
    }

    #[Route('/admin/validation/rejected/textsearch/{gasStationId}', name: 'app_admin_validation_rejected_textsearch')]
    public function rejectedToTextSearch(EntityManagerInterface $em, MessageBusInterface $messageBus, GasStation $gasStation): Response
    {
        if ($gasStation->getStatus() !== GasStationStatusReference::WAITING_VALIDATION) {
            return $this->redirect('/admin?routeName=app_admin_validation');
        }

        $gasStation->setStatus(GasStationStatusReference::VALIDATION_REJECTED);

        $em->persist($gasStation);
        $em->flush();

        $messageBus->dispatch(
            new CreateGooglePlaceTextsearchMessage(new GasStationId($gasStation->getGasStationId()))
        );

        return $this->redirect('/admin?routeName=app_admin_validation');
    }
}