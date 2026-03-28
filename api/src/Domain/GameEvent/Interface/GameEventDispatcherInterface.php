<?php

namespace App\Domain\GameEvent\Interface;

use App\Entity\Event\Game\GameEvent;
use App\Entity\Game\Game;

interface GameEventDispatcherInterface
{
    public function dispatch(GameEvent $gameEvent): Game;
}
