<?php

namespace App\Controller;

use App\Repository\AddressRepository;
use App\Repository\GasStationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(GasStationRepository $gasStationRepository): Response
    {
        dd($gasStationRepository->findOneBy(['gasStationId' => 94542003]));

        return $this->render('Home/index.html.twig', []);
    }
}
