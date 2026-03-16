<?php

namespace App\Domain\GameEvent;

use App\Domain\GameEvent\Interface\GameEventCollectorInterface;
use App\Entity\Event\Game\GameEvent;

class GameEventCollector implements GameEventCollectorInterface
{
    /** @var GameEvent[] */
    private array $events = [];

    public function collect(GameEvent $event): void
    {
        $this->events[] = $event;
    }

    public function getEvents(): array
    {
        return $this->events;
    }
}
