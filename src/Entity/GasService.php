<?php

namespace App\Entity;

use App\Repository\GasServiceRepository;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Entity\Traits\NameTraits;
use App\Service\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToMany(targetEntity: GasStation::class, inversedBy: 'gasServices', fetch: 'EXTRA_LAZY')]
    private Collection $gasStations;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->gasStations = new ArrayCollection();
    }

    /**
     * @return Collection<int, GasStation>
     */
    public function getGasStations(): Collection
    {
        return $this->gasStations;
    }

    public function addGasStation(GasStation $gasStation): static
    {
        if (!$this->gasStations->contains($gasStation)) {
            $this->gasStations->add($gasStation);
        }

        return $this;
    }

    public function removeGasStation(GasStation $gasStation): static
    {
        $this->gasStations->removeElement($gasStation);

        return $this;
    }
}
