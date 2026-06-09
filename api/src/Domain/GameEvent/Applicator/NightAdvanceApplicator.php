<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Workflow\NightOrchestrator;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Enum\Game\GameRuntimeStepEnum;

/** @implements GameEventApplicatorInterface<TimeUpGameEvent> */
class NightAdvanceApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;

    public function __construct(
        private readonly NightOrchestrator $nightOrchestrator,
    ) {
    }

    public static function getPriority(): int
    {
        return static::LAST_APPLY_PRIORITY;
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);

        return $this->nightOrchestrator->advance($game);
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof TimeUpGameEvent
            && GameRuntimeStepEnum::NIGHT === $gameEvent->getGame()?->getRuntimeStep();
    }
}
