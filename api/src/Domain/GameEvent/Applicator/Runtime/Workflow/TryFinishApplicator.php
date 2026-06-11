<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Workflow;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Workflow\GameFinisher;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Enum\Game\GameRuntimeStepEnum;

/** @implements GameEventApplicatorInterface<TimeUpGameEvent> */
class TryFinishApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;

    public function __construct(
        private readonly GameFinisher $gameFinisher,
    ) {
    }

    public static function getPriority(): int
    {
        return static::TRY_FINISH_PRIORITY;
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);

        $this->gameFinisher->tryFinish($game);

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof TimeUpGameEvent
            && \in_array(
                $gameEvent->getGame()?->getRuntimeStep(),
                [GameRuntimeStepEnum::NIGHT, GameRuntimeStepEnum::DAY, GameRuntimeStepEnum::VOTE],
            );
    }
}
