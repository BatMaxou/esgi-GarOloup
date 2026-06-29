<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Workflow;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Interceptor\InterceptorHandler;
use App\Domain\Workflow\DayOrchestrator;
use App\Domain\Workflow\NightOrchestrator;
use App\Domain\Workflow\VoteResolver;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Enum\Game\GameRuntimeStepEnum;

/** @implements GameEventApplicatorInterface<TimeUpGameEvent> */
class StartNextStepApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;

    public function __construct(
        private readonly DayOrchestrator $dayOrchestrator,
        private readonly VoteResolver $voteResolver,
        private readonly NightOrchestrator $nightOrchestrator,
        private readonly InterceptorHandler $interceptorHandler,
    ) {
    }

    public static function getPriority(): int
    {
        return static::POST_TRY_FINISH_PRIORITY;
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);

        $lastNight = $game->getNights()->last();
        $lastDay = $game->getDays()->last();
        $lastVote = $game->getVotes()->last();

        $isNightFinished = GameRuntimeStepEnum::NIGHT === $game->getRuntimeStep() && $lastNight && $lastNight->isResolved();
        $isDayFinished = GameRuntimeStepEnum::DAY === $game->getRuntimeStep() && $lastDay && $lastDay->isResolved();
        $isVoteFinished = GameRuntimeStepEnum::VOTE === $game->getRuntimeStep() && $lastVote && $lastVote->isResolved();

        if (GameRuntimeStepEnum::INTERUPT === $game->getRuntimeStep()) {
            if ($this->interceptorHandler->hasPendingAction($game)) {
                return $game;
            }

            $interruptedStep = $game->getInterruptedRuntimeStep();
            $game->setInterruptedRuntimeStep(null);
            $game->setInterruptedByRole(null);

            $isNightFinished = GameRuntimeStepEnum::NIGHT === $interruptedStep;
            $isDayFinished = GameRuntimeStepEnum::DAY === $interruptedStep;
            $isVoteFinished = GameRuntimeStepEnum::VOTE === $interruptedStep;
        }

        if ($isNightFinished) {
            $this->dayOrchestrator->start($game);
        } elseif ($isDayFinished) {
            $this->voteResolver->start($game);
        } elseif ($isVoteFinished) {
            $this->nightOrchestrator->start($game);
        }

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof TimeUpGameEvent
            && \in_array(
                $gameEvent->getGame()?->getRuntimeStep(),
                [GameRuntimeStepEnum::NIGHT, GameRuntimeStepEnum::DAY, GameRuntimeStepEnum::VOTE, GameRuntimeStepEnum::INTERUPT],
            );
    }
}
