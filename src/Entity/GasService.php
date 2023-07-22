<?php

namespace App\Entity;

use App\Repository\GasServiceRepository;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Entity\Traits\NameTraits;
use App\Service\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\Blameable;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\Timestampable;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: GasServiceRepository::class)]
#[ApiResource]
class GasService
{
    use IdentifyTraits;
    use NameTraits;
    use TimestampableEntity;
    use BlameableEntity;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }
}
