<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Workflow\GameFinisher;
use App\Domain\Workflow\NightOrchestrator;
use App\Domain\Workflow\VoteResolver;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Enum\Game\GameRuntimeStepEnum;

/** @implements GameEventApplicatorInterface<TimeUpGameEvent> */
class VoteResolveApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;

    public function __construct(
        private readonly VoteResolver $voteResolver,
        private readonly NightOrchestrator $nightOrchestrator,
        private readonly GameFinisher $gameFinisher,
    ) {
    }

    public static function getPriority(): int
    {
        return static::LAST_APPLY_PRIORITY;
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);

        $this->voteResolver->resolve($game);

        if ($this->gameFinisher->tryFinish($game)) {
            return $game;
        }

        $this->nightOrchestrator->start($game);

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof TimeUpGameEvent
            && GameRuntimeStepEnum::VOTE === $gameEvent->getGame()?->getRuntimeStep();
    }
}
