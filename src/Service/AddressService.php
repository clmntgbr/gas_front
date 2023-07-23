<?php

namespace App\Service;

use App\Entity\Address;
use Doctrine\ORM\EntityManagerInterface;

final class AddressService
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function hydrate(Address $address, array $data): void
    {
        $address
            ->setCity($data['locality'] ?? $address->getCity())
            ->setCountry($data['country'] ?? $address->getCountry())
            ->setLongitude($data['longitude'] ?? $address->getLongitude())
            ->setLatitude($data['latitude'] ?? $address->getLatitude())
            ->setStreet($data['street'] ?? $address->getStreet())
            ->setVicinity($data['label'] ?? $address->getVicinity())
            ->setNumber($data['number'] ?? $address->getNumber())
            ->setRegion($data['region'] ?? $address->getRegion())
            ->setPostalCode($data['postal_code'] ?? $address->getPostalCode());

        $this->em->persist($address);
        $this->em->flush();
    }
}
