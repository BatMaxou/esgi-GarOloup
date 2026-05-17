<?php

namespace App\Entity\Event\Game;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class WerewolfVoteEvent extends GameEvent
{
    #[ORM\Column(length: 36)]
    private string $targetPlayerId;

    public function getTargetPlayerId(): string
    {
        return $this->targetPlayerId;
    }

    public function setTargetPlayerId(string $targetPlayerId): static
    {
        $this->targetPlayerId = $targetPlayerId;

        return $this;
    }
}
