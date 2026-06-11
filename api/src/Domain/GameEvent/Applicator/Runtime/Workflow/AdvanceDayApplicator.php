<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Workflow;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Workflow\DayOrchestrator;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Enum\Game\GameRuntimeStepEnum;

/** @implements GameEventApplicatorInterface<TimeUpGameEvent> */
class AdvanceDayApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;

    public function __construct(
        private readonly DayOrchestrator $dayOrchestrator,
    ) {
    }

    public static function getPriority(): int
    {
        return static::WORKFLOW_ADVANCE_PRIORITY;
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);

        return $this->dayOrchestrator->advance($game);
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof TimeUpGameEvent
            && GameRuntimeStepEnum::DAY === $gameEvent->getGame()?->getRuntimeStep();
    }
}
