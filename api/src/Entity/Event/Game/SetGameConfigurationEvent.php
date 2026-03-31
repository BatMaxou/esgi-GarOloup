<?php

namespace App\Entity\Event\Game;

use App\Api\Model\Game\Composition\CompositionInput;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class SetGameConfigurationEvent extends GameEvent
{
    #[ORM\Column]
    private bool $withGameMaster = false;

    #[ORM\Column]
    private bool $withRandomDispatch = false;

    private ?CompositionInput $composition = null;

    public function isWithGameMaster(): bool
    {
        return $this->withGameMaster;
    }

    public function setWithGameMaster(bool $withGameMaster): static
    {
        $this->withGameMaster = $withGameMaster;

        return $this;
    }

    public function isWithRandomDispatch(): bool
    {
        return $this->withRandomDispatch;
    }

    public function setWithRandomDispatch(bool $withRandomDispatch): static
    {
        $this->withRandomDispatch = $withRandomDispatch;

        return $this;
    }

    public function getComposition(): ?CompositionInput
    {
        return $this->composition;
    }

    public function setComposition(?CompositionInput $composition): static
    {
        $this->composition = $composition;

        return $this;
    }
}
