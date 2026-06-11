<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Workflow;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Workflow\VoteResolver;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Enum\Game\GameRuntimeStepEnum;

/** @implements GameEventApplicatorInterface<TimeUpGameEvent> */
class ResolveVoteApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;

    public function __construct(
        private readonly VoteResolver $voteResolver,
    ) {
    }

    public static function getPriority(): int
    {
        return static::WORKFLOW_ADVANCE_PRIORITY;
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);

        $lastVote = $game->getVotes()->last();
        if ($lastVote && $lastVote->isResolved()) {
            return $game;
        }

        return $this->voteResolver->resolve($game);
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof TimeUpGameEvent
            && GameRuntimeStepEnum::VOTE === $gameEvent->getGame()?->getRuntimeStep();
    }
}
