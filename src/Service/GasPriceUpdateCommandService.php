<?php

namespace App\Service;

use App\Entity\EntityId\GasStationId;
use App\Message\CreateGasStationMessage;
use App\Repository\GasStationRepository;
use Safe;
use Symfony\Component\Messenger\MessageBus;

class GasPriceUpdateCommandService
{
    public function __construct(
        private readonly string $gasPricePath,
        private readonly string $gasPriceJsonName,
        private readonly GasStationRepository $gasStationRepository,
        private readonly GasStationService $gasStationService,
        private readonly MessageBus $messageBus,
    ) {
    }

    public function invoke(): void
    {
        if (!FileSystemService::exist($this->gasPricePath, $this->gasPriceJsonName)) {
            throw new \Exception('json gas prices dont exist.');
        }

        $file = fopen("$this->gasPricePath/$this->gasPriceJsonName", 'r');

        $content = fread($file, filesize("$this->gasPricePath/$this->gasPriceJsonName"));
        fclose($file);

        $data = Safe\json_decode($content, true);

        $gasStations = $this->gasStationRepository->findGasStationsById();

        foreach ($data as $datum) {
            $gasStationId = $this->gasStationService->getGasStationId($datum['@attributes']['id']);

            if (!in_array(substr($gasStationId->getId(), 0, 2), ['94'])) { // for dev only
                continue;
            }

            $hash = $this->getHash($datum);

            if (!array_key_exists($gasStationId->getId(), $gasStations)) {
                $this->createGasStationMessage($gasStationId, $hash, $datum);
            }
        }
    }

    private function getHash(array $datum): string
    {
        $element = $datum;
        unset($element['prix']);
        return hash('sha256', json_encode($element));
    }

    private function createGasStationMessage(GasStationId $gasStationId, string $hash, array $datum)
    {
        $this->messageBus->dispatch(
            new CreateGasStationMessage(
                $gasStationId,
                $this->convert($datum['@attributes']['pop'] ?? ''),
                $hash,
                $this->convert($datum['@attributes']['cp'] ?? ''),
                $this->convert($datum['@attributes']['longitude'] ?? ''),
                $this->convert($datum['@attributes']['latitude'] ?? ''),
                $this->convert($datum['adresse'] ?? ''),
                $this->convert($datum['ville'] ?? ''),
                'FRANCE',
                $datum,
            ),
        );
    }

    private function convert($datum): string
    {
        if (is_array($datum)) {
            return implode(' ', $datum);
        }

        return $datum;
    }
}
