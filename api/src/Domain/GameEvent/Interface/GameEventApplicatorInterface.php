<?php

namespace App\Domain\GameEvent\Interface;

use App\Entity\Event\Game\GameEvent;
use App\Entity\Game;

/** @template T of GameEvent */
interface GameEventApplicatorInterface
{
    public const int DEFAULT_PRIORITY = 10;

    /** @param T $gameEvent */
    public function apply(GameEvent $gameEvent): Game;

    public function supports(GameEvent $gameEvent): bool;

    public static function getPriority(): int;
}
