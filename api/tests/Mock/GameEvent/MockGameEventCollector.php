<?php

namespace App\Tests\Mock\GameEvent;

use App\Domain\GameEvent\GameEventCollector;
use App\Domain\GameEvent\Interface\GameEventCollectorInterface;

class MockGameEventCollector extends GameEventCollector implements GameEventCollectorInterface
{
    public function hasCollected(string $gameEventClass, string $playerUsername, string $gameId): bool
    {
        foreach ($this->getEvents() as $gameEvent) {
            if (
                $gameEvent instanceof $gameEventClass
                && $gameEvent->getPlayerUsername() === $playerUsername
                && $gameEvent->getGameId() === $gameId
            ) {
                return true;
            }
        }

        return false;
    }

    public function getEventCollectedNumber(): int
    {
        return \count($this->getEvents());
    }
}
