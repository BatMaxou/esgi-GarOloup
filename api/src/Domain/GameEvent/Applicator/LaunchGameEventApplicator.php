<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\GameSpec;
use App\Domain\Workflow\NightWorkflowComposer;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\LaunchGameEvent;
use App\Entity\Game\Game;
use App\Enum\Game\GameRuntimeStepEnum;
use Symfony\Component\Clock\ClockInterface;

/** @implements GameEventApplicatorInterface<LaunchGameEvent> */
class LaunchGameEventApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly GameSpec $gameSpec,
        private readonly NightWorkflowComposer $nightWorkflowComposer,
        private readonly ClockInterface $clock,
        private readonly int $setupDuration,
    ) {
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $user = $this->ensureUser($gameEvent);
        $game = $this->ensureGame($gameEvent);

        if (!$this->gameSpec->canLaunchGame($user, $game)) {
            throw new UnauthorizedGameActionException('You can not launch this game');
        }

        $game->setRuntimeStep(GameRuntimeStepEnum::SETUP);
        $game->setStepEndAt($this->clock->now()->modify(\sprintf('+%d seconds', $this->setupDuration)));

        $game->setWorkflow($this->nightWorkflowComposer->for($game));

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof LaunchGameEvent;
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }
}
