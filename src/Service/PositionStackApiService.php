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
        $url = sprintf('%sforward?access_key=%s&query=%s', $this->positionStackUrl, $this->positionStackApiKey, urlencode($address->getVicinity()));
        $response = $client->request('GET', $url);

        $data = \Safe\json_decode($response->getBody()->getContents(), true);

        if (!array_key_exists('data', $data)) {
            return null;
        }

        if (!array_key_exists(0, $data['data'])) {
            return null;
        }

        $datum = $data['data'][0];
        $datum['forwardUrl'] = $url;
        return $datum;
    }

    public function reverse(Address $address): ?array
    {
        $client = new Client();
        $url = sprintf('%sreverse?access_key=%s&query=%s,%s', $this->positionStackUrl, $this->positionStackApiKey, $address->getLatitude(), $address->getLongitude());
        $response = $client->request('GET', $url);

        $data = \Safe\json_decode($response->getBody()->getContents(), true);

        if (!array_key_exists('data', $data)) {
            return null;
        }

        if (!array_key_exists(0, $data['data'])) {
            return null;
        }

        $datum = $data['data'][0];
        $datum['reverseUrl'] = $url;
        return $datum;
    }
}
