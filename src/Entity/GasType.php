<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Entity\Traits\NameTraits;
use App\Repository\GasTypeRepository;
use App\Service\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\Blameable;
use Gedmo\Timestampable\Traits\Timestampable;

#[ORM\Entity(repositoryClass: GasTypeRepository::class)]
#[ApiResource]
class GasType
{
    use IdentifyTraits;
    use NameTraits;
    use Timestampable;
    use Blameable;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }
}
