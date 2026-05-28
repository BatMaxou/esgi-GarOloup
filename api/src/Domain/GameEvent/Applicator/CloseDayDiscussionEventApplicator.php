<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Workflow\DayOrchestrator;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Enum\Game\GameRuntimeStepEnum;

/** @implements GameEventApplicatorInterface<TimeUpGameEvent> */
class CloseDayDiscussionEventApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;

    public function __construct(
        private readonly DayOrchestrator $dayOrchestrator,
    ) {
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);
        $day = $game->getCurrentDay() ?? throw new \LogicException('No active day to resolve');

        return $this->dayOrchestrator->resolve($game, $day);
    }

    public function supports(GameEvent $gameEvent): bool
    {
        $game = $gameEvent->getGame();

        return $gameEvent instanceof TimeUpGameEvent
            && $game
            && GameRuntimeStepEnum::DAY === $game->getRuntimeStep()
        ;
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }
}
