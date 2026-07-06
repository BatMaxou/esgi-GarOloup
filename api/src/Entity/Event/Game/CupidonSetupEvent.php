<?php

namespace App\Entity\Event\Game;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CupidonSetupEvent extends GameEvent
{
    #[ORM\Column(length: 36)]
    private string $firstLoverId;

    #[ORM\Column(length: 36)]
    private string $secondLoverId;

    public function getFirstLoverId(): string
    {
        return $this->firstLoverId;
    }

    public function setFirstLoverId(string $firstLoverId): static
    {
        $this->firstLoverId = $firstLoverId;

        return $this;
    }

    public function getSecondLoverId(): string
    {
        return $this->secondLoverId;
    }

    public function setSecondLoverId(string $secondLoverId): static
    {
        $this->secondLoverId = $secondLoverId;

        return $this;
    }
}
