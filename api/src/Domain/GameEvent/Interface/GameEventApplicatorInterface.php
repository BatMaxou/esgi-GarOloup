<?php

namespace App\Domain\GameEvent\Interface;

use App\Entity\Event\Game\GameEvent;
use App\Entity\Game\Game;

/** @template T of GameEvent */
interface GameEventApplicatorInterface
{
    public const int PRE_APPLY_PRIORITY = 0;
    public const int DEFAULT_PRIORITY = 50;
    public const int POST_APPLY_PRIORITY = 100;

    /** @param T $gameEvent */
    public function apply(GameEvent $gameEvent): Game;

    public function supports(GameEvent $gameEvent): bool;

    public static function getPriority(): int;
}
