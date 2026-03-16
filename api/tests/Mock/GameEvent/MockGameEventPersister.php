<?php

namespace App\Tests\Mock\GameEvent;

use App\Domain\GameEvent\Interface\GameEventPersisterInterface;
use App\Entity\Event\Game\GameEvent;

class MockGameEventPersister implements GameEventPersisterInterface
{
    public function persist(GameEvent $gameEvent): void
    {
    }
}
