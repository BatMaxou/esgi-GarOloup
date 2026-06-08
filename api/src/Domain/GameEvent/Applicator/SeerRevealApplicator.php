<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\RandomizationAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\Role\SeerSpec;
use App\Domain\Workflow\NightOrchestrator;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\SeerRevealEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Role\SeerRole;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Repository\Game\PlayerRepository;
use Symfony\Component\Uid\Uuid;

/** @implements GameEventApplicatorInterface<SeerRevealEvent|TimeUpGameEvent> */
class SeerRevealApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;
    /** @use RandomizationAwareTrait<SeerRevealEvent, TimeUpGameEvent> */
    use RandomizationAwareTrait;

    public function __construct(
        private readonly SeerSpec $seerSpec,
        private readonly PlayerRepository $playerRepository,
        private readonly NightOrchestrator $nightOrchestrator,
    ) {
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }

    protected function applyAction(GameEvent $gameEvent): Game
    {
        $user = $this->ensureUser($gameEvent);
        $game = $this->ensureGame($gameEvent);

        $player = $this->playerRepository->findCurrentByUser($user);
        if (null === $player) {
            throw new PlayerNotFoundException('Current player not found');
        }

        $targetPlayerId = $gameEvent->getTargetPlayerId();
        if (!Uuid::isValid($targetPlayerId)) {
            throw new PlayerNotFoundException('Target player not found');
        }

        $targetPlayer = $this->playerRepository->find($targetPlayerId);
        if (null === $targetPlayer || $game !== $targetPlayer->getGame()) {
            throw new PlayerNotFoundException('Target player not found');
        }

        if (!$this->seerSpec->canReveal($player, $game, $targetPlayer)) {
            throw new UnauthorizedGameActionException('You can not reveal');
        }

        $role = $player->getRole();
        if (!$role instanceof SeerRole) {
            throw new \LogicException(\sprintf('Role must be verified as a %s here', SeerRole::class));
        }

        $role->observe($targetPlayer);

        return $game;
    }

    protected function randomizeAction(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);

        return $this->nightOrchestrator->advance($game);
    }

    protected function supportsRandomization(GameEvent $gameEvent): bool
    {
        $game = $gameEvent->getGame();
        if (!$gameEvent instanceof TimeUpGameEvent || null === $game) {
            return false;
        }

        if (GameRuntimeStepEnum::NIGHT !== $game->getRuntimeStep()) {
            return false;
        }

        $workflow = $game->getNightWorkflow();

        return null !== $workflow && \in_array(GameRoleEnum::SEER, $workflow->getCurrentTurn(), true);
    }

    protected function supportsAction(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof SeerRevealEvent;
    }
}
