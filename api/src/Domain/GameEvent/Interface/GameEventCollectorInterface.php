<?php

namespace App\Domain\GameEvent\Interface;

use App\Entity\Event\Game\GameEvent;

interface GameEventCollectorInterface
{
    public function collect(GameEvent $event): void;

    /** @return GameEvent[] */
    public function getEvents(): array;
}
