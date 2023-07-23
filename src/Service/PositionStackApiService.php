<?php

namespace App\Service;

use App\Entity\Address;
use GuzzleHttp\Client;

class PositionStackApiService
{
    public function __construct(
        private readonly string $positionStackApiKey,
        private readonly string $positionStackUrl
    ) {
    }

    public function forward(Address $address): ?array
    {
        $client = new Client();
        $response = $client->request('GET', sprintf('%sforward?access_key=%s&query=%s', $this->positionStackUrl, $this->positionStackApiKey, urlencode($address->getVicinity())));

        $data = \Safe\json_decode($response->getBody()->getContents(), true);

        if (!array_key_exists('data', $data)) {
            return null;
        }

        if (!array_key_exists(0, $data['data'])) {
            return null;
        }

        return $data['data'][0];
    }

    public function reverse(Address $address): ?array
    {
        $client = new Client();
        $response = $client->request('GET', sprintf('%sreverse?access_key=%s&query=%s,%s', $this->positionStackUrl, $this->positionStackApiKey, $address->getLatitude(), $address->getLongitude()));

        $data = \Safe\json_decode($response->getBody()->getContents(), true);

        if (!array_key_exists('data', $data)) {
            return null;
        }

        if (!array_key_exists(0, $data['data'])) {
            return null;
        }

        return $data['data'][0];
    }
}
