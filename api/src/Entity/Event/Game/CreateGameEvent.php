<?php

namespace App\Entity\Event\Game;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CreateGameEvent extends GameEvent
{
    #[ORM\Column]
    private int $maxPlayers;

    #[ORM\Column]
    private int $maxTimeForDiscussion;

    #[ORM\Column]
    private bool $public;

    public function getMaxPlayers(): int
    {
        return $this->maxPlayers;
    }

    public function setMaxPlayers(int $maxPlayers): static
    {
        $this->maxPlayers = $maxPlayers;

        return $this;
    }

    public function getMaxTimeForDiscussion(): int
    {
        return $this->maxTimeForDiscussion;
    }

    public function setMaxTimeForDiscussion(int $maxTimeForDiscussion): static
    {
        $this->maxTimeForDiscussion = $maxTimeForDiscussion;

        return $this;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): static
    {
        $this->public = $public;

        return $this;
    }
}
