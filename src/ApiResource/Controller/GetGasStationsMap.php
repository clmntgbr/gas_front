<?php

namespace App\ApiResource\Controller;

use App\Repository\GasStationRepository;
use App\Service\GasStationsMapService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Safe;

#[AsController]
class GetGasStationsMap extends AbstractController
{
    public static string $operationName = 'get_gas_stations_map';

    public function __construct(
        private GasStationRepository $gasStationRepository,
        private GasStationsMapService $gasStationsMapService
    ) {
    }

    public function __invoke(Request $request)
    {
        $latitude = $request->query->get('latitude') ?? 48.764977;
        $longitude = $request->query->get('longitude') ?? 2.358192;
        $radius = $request->query->get('radius') ?? 50000;
        $gasTypeUuid = $request->query->get('gasTypeUuid') ?? '1';
        $filterCity = Safe\json_decode($request->query->get('filter_city') ?? "[]", true);

        $gasStations = $this->gasStationRepository->getGasStationsMap($longitude, $latitude, $radius, $gasTypeUuid, $filterCity);
        return $this->gasStationsMapService->invoke($gasStations, $gasTypeUuid);
    }
}
