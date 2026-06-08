<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\RandomizationAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\Role\WitchSpec;
use App\Domain\Workflow\NightOrchestrator;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Event\Game\WitchPoisonEvent;
use App\Entity\Event\Game\WitchSaveEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\NightAction\MurderAction;
use App\Entity\Game\Period\Action\NightAction\SaveAction;
use App\Entity\Game\Role\WitchRole;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Repository\Game\PlayerRepository;
use Symfony\Component\Uid\Uuid;

/** @implements GameEventApplicatorInterface<WitchSaveEvent|WitchPoisonEvent|TimeUpGameEvent> */
class WitchApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;
    /** @use RandomizationAwareTrait<WitchSaveEvent|WitchPoisonEvent, TimeUpGameEvent> */
    use RandomizationAwareTrait;

    public function __construct(
        private readonly WitchSpec $witchSpec,
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

        $night = $game->getCurrentNight() ?? throw new \LogicException('No active night to register the witch action');

        $role = $player->getRole();

        if ($gameEvent instanceof WitchSaveEvent) {
            if (!$this->witchSpec->canSave($player, $game, $targetPlayer)) {
                throw new UnauthorizedGameActionException('You can not save');
            }

            if (!$role instanceof WitchRole) {
                throw new \LogicException(\sprintf('Role must be verified as a %s here', WitchRole::class));
            }

            $night->addAction(new SaveAction($night, GameRoleEnum::WITCH, $targetPlayerId));
            $role->useHealPotion();

            return $game;
        }

        if (!$this->witchSpec->canPoison($player, $game, $targetPlayer)) {
            throw new UnauthorizedGameActionException('You can not poison');
        }

        if (!$role instanceof WitchRole) {
            throw new \LogicException(\sprintf('Role must be verified as a %s here', WitchRole::class));
        }

        $night->addAction(new MurderAction($night, GameRoleEnum::WITCH, $targetPlayerId));
        $role->usePoisonPotion();

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

        return null !== $workflow && \in_array(GameRoleEnum::WITCH, $workflow->getCurrentTurn(), true);
    }

    protected function supportsAction(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof WitchSaveEvent || $gameEvent instanceof WitchPoisonEvent;
    }
}
