<?php

namespace App\Domain\GameEvent\Applicator\Trait;

use App\Entity\Event\Game\GameEvent;
use App\Entity\Game\Game;

/**
 * @template Default of GameEvent
 * @template Randomizable of GameEvent
 */
trait RandomizationAwareTrait
{
    public function supports(GameEvent $gameEvent): bool
    {
        return $this->supportsAction($gameEvent) || $this->supportsRandomization($gameEvent);
    }

    public function apply(GameEvent $gameEvent): Game
    {
        if ($this->supportsRandomization($gameEvent)) {
            /** @var Randomizable $gameEvent */
            return $this->randomizeAction($gameEvent);
        }

        /** @var Default $gameEvent */
        return $this->applyAction($gameEvent);
    }

    abstract protected function supportsRandomization(GameEvent $gameEvent): bool;

    abstract protected function supportsAction(GameEvent $gameEvent): bool;

    /** @param Default $gameEvent */
    abstract protected function applyAction(GameEvent $gameEvent): Game;

    /** @param Randomizable $gameEvent */
    abstract protected function randomizeAction(GameEvent $gameEvent): Game;
}
