<?php

namespace App\Controller;

use App\Repository\GasStationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(GasStationRepository $gasStationRepository, EntityManagerInterface $em): Response
    {
        $gasStation = $gasStationRepository->findOneBy(['gasStationId' => 94100005]);
        foreach ($gasStation->getGasServices() as $service) {
            $gasStation->removeGasService($service);
        }

        $em->persist($gasStation);
        $em->flush();

        dd($gasStation);
        return $this->render('Home/index.html.twig', []);
    }
}
