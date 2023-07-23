<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait NameTraits
{
    #[ORM\Column(type: Types::STRING)]
    private ?string $name;

    #[ORM\Column(type: Types::STRING)]
    private ?string $reference;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference)
    {
        $this->reference = $reference;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
