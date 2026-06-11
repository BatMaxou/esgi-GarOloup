<?php

namespace App\Domain\GameEvent\Interface;

use App\Entity\Event\Game\GameEvent;
use App\Entity\Game\Game;

/** @template T of GameEvent */
interface GameEventApplicatorInterface
{
    public const int FIRST_APPLY_PRIORITY = 0;
    public const int DEFAULT_PRIORITY = 20;
    public const int PRE_WORKFLOW_ADVANCE_PRIORITY = 30;
    public const int WORKFLOW_ADVANCE_PRIORITY = 40;
    public const int POST_WORKFLOW_ADVANCE_PRIORITY = 50;
    public const int PRE_TRY_FINISH_PRIORITY = 60;
    public const int TRY_FINISH_PRIORITY = 70;
    public const int POST_TRY_FINISH_PRIORITY = 80;
    public const int LAST_APPLY_PRIORITY = 150;

    /** @param T $gameEvent */
    public function apply(GameEvent $gameEvent): Game;

    public function supports(GameEvent $gameEvent): bool;

    public static function getPriority(): int;
}
