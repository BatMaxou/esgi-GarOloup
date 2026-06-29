<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Role\Hunter;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Interceptor\HunterInterceptor;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Enum\Game\GameRuntimeStepEnum;
use Symfony\Component\Clock\ClockInterface;

/** @implements GameEventApplicatorInterface<TimeUpGameEvent> */
class HunterInterruptApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;

    public function __construct(
        private readonly HunterInterceptor $hunterInterceptor,
        private readonly ClockInterface $clock,
        private readonly int $hunterStepDuration,
    ) {
    }

    public static function getPriority(): int
    {
        return static::PRE_TRY_FINISH_PRIORITY;
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);

        if (!$this->hunterInterceptor->hasPendingAction($game)) {
            return $game;
        }

        $game->setInterruptedRuntimeStep($game->getRuntimeStep());
        $game->setInterruptedByRole($this->hunterInterceptor->getRole());
        $game->setRuntimeStep(GameRuntimeStepEnum::INTERUPT);
        $game->setStepEndAt($this->clock->now()->modify(\sprintf('+%d seconds', $this->hunterStepDuration)));

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof TimeUpGameEvent
            && \in_array(
                $gameEvent->getGame()?->getRuntimeStep(),
                [GameRuntimeStepEnum::NIGHT, GameRuntimeStepEnum::DAY, GameRuntimeStepEnum::VOTE],
                true,
            );
    }
}
