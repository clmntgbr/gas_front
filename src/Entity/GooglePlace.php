<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Repository\GooglePlaceRepository;
use App\Service\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\Blameable;
use Gedmo\Timestampable\Traits\Timestampable;

#[ORM\Entity(repositoryClass: GooglePlaceRepository::class)]
#[ApiResource]
class GooglePlace
{
    use IdentifyTraits;
    use Timestampable;
    use Blameable;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }
}
