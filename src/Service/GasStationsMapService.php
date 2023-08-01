<?php

namespace App\Service;

use App\Entity\GasStation;

final class GasStationsMapService
{
    public function __construct(
        private array $lowGasPrices = []
    ) {
    }

    /** @param GasStation[] $gasStations */
    public function invoke($gasStations, string $gasTypeId)
    {
        foreach ($gasStations as $key => $gasStation) {
            foreach ($gasStation->getLastGasPrices() as $gasPriceKey => $gasPrice) {
                if (array_key_exists('gasTypeId', $gasPrice) && $gasPrice['gasTypeId'] !== $gasTypeId) {
                    continue;
                }
                if (!array_key_exists($key, $this->lowGasPrices)) {
                    $this->updateLowGasPrices($gasStation, $key, $gasPriceKey, $gasPrice);
                    continue;
                }
                if (array_key_exists('gasPriceValue', $gasPrice) && array_key_exists('gasPriceValue', $this->lowGasPrices[$key]) && $gasPrice['gasPriceValue'] <= $this->lowGasPrices[$key]['gasPriceValue']) {
                    $this->updateLowGasPrices($gasStation, $key, $gasPriceKey, $gasPrice);
                    continue;
                }
            }
        }

        foreach ($this->lowGasPrices as $key => $lowGasPrice) {
            $gasStation = $gasStations[$lowGasPrice['gasStationIndex']];
            $gasStation->setHasLowPrices(true);

            $lastGasPrices = $gasStation->getLastGasPrices();

            if (array_key_exists($key, $lastGasPrices)) {
                $prices = $lastGasPrices[$key];
                $prices['isLowPrice'] = true;
                $lastGasPrices[$key] = $prices;
                $gasStation->addLastGasPrices($lastGasPrices);
            }

            $gasStations[$lowGasPrice['gasStationIndex']] = $gasStation;
        }

        return $gasStations;
    }

    private function updateLowGasPrices(GasStation $gasStation, int $key, string $gasPriceKey, array $gasPrice)
    {
        $this->lowGasPrices[$gasPriceKey] = [
            'id' => $gasPrice['gasPriceId'],
            'gasStationId' => $gasStation->getId(),
            'gasStationIndex' => $key,
        ];
    }
}
