<?php

namespace App\Domain\GameEvent\Interface;

use App\Entity\Event\Game\GameEvent;

interface GameEventPersisterInterface
{
    public function persist(GameEvent $gameEvent): void;
}
