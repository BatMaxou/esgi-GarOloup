<?php

namespace App\Domain\GameEvent\Applicator\Trait;

use App\Domain\GameEvent\Exception\MissingGameException;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Game;

trait GameAwareTrait
{
    private function ensureGame(GameEvent $event): Game
    {
        $game = $event->getGame();
        if (!$game) {
            throw new MissingGameException('Game must be seto at this step');
        }

        return $game;
    }
}
