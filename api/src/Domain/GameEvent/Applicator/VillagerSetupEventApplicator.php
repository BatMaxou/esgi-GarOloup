<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\AfkAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\RandomizationAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\GameSpec;
use App\Domain\Spec\Role\VillagerSpec;
use App\Domain\Workflow\NightOrchestrator;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Event\Game\VillagerSetupEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Role\VillagerRole;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Repository\Game\PlayerRepository;

/** @implements GameEventApplicatorInterface<VillagerSetupEvent|TimeUpGameEvent> */
class VillagerSetupEventApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;
    use AfkAwareTrait;
    /** @use RandomizationAwareTrait<VillagerSetupEvent, TimeUpGameEvent> */
    use RandomizationAwareTrait;

    public function __construct(
        private readonly GameSpec $gameSpec,
        private readonly VillagerSpec $villagerSpec,
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

        if (!$this->villagerSpec->canChooseFriend($player, $game, $targetPlayerId)) {
            throw new UnauthorizedGameActionException('You can not choose a friend');
        }

        $role = $player->getRole();
        if (!$role instanceof VillagerRole) {
            throw new UnauthorizedGameActionException('You can not choose a friend');
        }

        $role->setFriendId($targetPlayerId);
        $role->setSetup(true);

        // bouger ca dans un applicator a priorité faible
        if ($this->gameSpec->areAllRolesSetup($game)) {
            $this->nightOrchestrator->start($game);
        }

        return $game;
    }

    protected function randomizeAction(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);
        $players = $game->getPlayers();

        foreach ($players as $player) {
            $role = $player->getRole();
            if ($role instanceof VillagerRole && !$role->isSetup()) {
                $role->setFriendId($this->getRandomPlayerId($game));
                $role->setSetup(true);
                $this->handleAfkPlayer($player);
            }
        }

        // same
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
        return $gameEvent instanceof VillagerSetupEvent;
    }

    protected function getAfkThreshold(): int
    {
        return $this->afkThreshold;
    }

    private function getRandomPlayerId(Game $game): string
    {
        $players = $game->getPlayers();
        $randomIndex = \array_rand($game->getPlayers()->toArray());
        $randomPlayer = $players[$randomIndex];
        $randomPlayerId = $randomPlayer?->getId()?->toString();
        if (!$randomPlayerId) {
            throw new \LogicException('Random should not be null here');
        }

        return $randomPlayerId;
    }
}
