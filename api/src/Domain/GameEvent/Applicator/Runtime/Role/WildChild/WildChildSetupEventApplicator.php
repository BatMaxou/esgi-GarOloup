<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Role\WildChild;

use App\Domain\GameEvent\Applicator\Trait\AfkAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\RandomizationAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\GameSpec;
use App\Domain\Spec\Role\WildChildSpec;
use App\Domain\Workflow\NightOrchestrator;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Event\Game\WildChildSetupEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Entity\Game\Role\WildChildRole;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Repository\Game\PlayerRepository;

/** @implements GameEventApplicatorInterface<WildChildSetupEvent|TimeUpGameEvent> */
class WildChildSetupEventApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;
    use AfkAwareTrait;
    /** @use RandomizationAwareTrait<WildChildSetupEvent, TimeUpGameEvent> */
    use RandomizationAwareTrait;

    public function __construct(
        private readonly GameSpec $gameSpec,
        private readonly WildChildSpec $wildChildSpec,
        private readonly PlayerRepository $playerRepository,
        private readonly NightOrchestrator $nightOrchestrator,
        private readonly int $afkThreshold,
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

        $targetPlayerId = $gameEvent->getTargetPlayerId();
        $player = $this->playerRepository->findCurrentByUser($user);

        if (null === $player) {
            throw new PlayerNotFoundException('Current player not found');
        }

        if (!$this->wildChildSpec->canChooseModel($player, $game, $targetPlayerId)) {
            throw new UnauthorizedGameActionException('You can not choose a model');
        }

        $role = $player->getRole();
        if (!$role instanceof WildChildRole) {
            throw new UnauthorizedGameActionException('You can not choose a model');
        }

        $role->setModelPlayerId($targetPlayerId);
        $role->setSetup(true);

        if ($this->gameSpec->areAllRolesSetup($game)) {
            $this->nightOrchestrator->start($game);
        }

        return $game;
    }

    protected function randomizeAction(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);

        foreach ($game->getPlayers() as $player) {
            $role = $player->getRole();
            if ($role instanceof WildChildRole && !$role->isSetup()) {
                $role->setModelPlayerId($this->getRandomModelId($game, $player));
                $role->setSetup(true);
                $this->handleAfkPlayer($player);
            }
        }

        if ($this->gameSpec->areAllRolesSetup($game)) {
            $this->nightOrchestrator->start($game);
        }

        return $game;
    }

    protected function supportsRandomization(GameEvent $gameEvent): bool
    {
        $game = $gameEvent->getGame();

        return $gameEvent instanceof TimeUpGameEvent
            && $game
            && GameRuntimeStepEnum::SETUP === $game->getRuntimeStep()
        ;
    }

    protected function supportsAction(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof WildChildSetupEvent;
    }

    protected function getAfkThreshold(): int
    {
        return $this->afkThreshold;
    }

    private function getRandomModelId(Game $game, Player $wildChild): string
    {
        $candidates = [];
        foreach ($game->getPlayers() as $player) {
            if ($player->getId()?->toString() !== $wildChild->getId()?->toString()) {
                $candidates[] = $player;
            }
        }

        $modelId = $candidates[\array_rand($candidates)]->getId()?->toString();
        if (!$modelId) {
            throw new \LogicException('Random model should not be null here');
        }

        return $modelId;
    }
}
