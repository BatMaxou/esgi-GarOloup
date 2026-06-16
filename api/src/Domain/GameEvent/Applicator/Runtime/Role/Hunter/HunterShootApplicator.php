<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Role\Hunter;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\Role\HunterSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\HunterShootEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Role\HunterRole;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Repository\Game\PlayerRepository;
use Symfony\Component\Uid\Uuid;

/** @implements GameEventApplicatorInterface<HunterShootEvent|TimeUpGameEvent> */
class HunterShootApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly HunterSpec $hunterSpec,
        private readonly PlayerRepository $playerRepository,
    ) {
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);

        if ($gameEvent instanceof TimeUpGameEvent) {
            foreach ($game->getPlayers() as $player) {
                $role = $player->getRole();
                if ($player->isDead() && $role instanceof HunterRole && !$role->hasShot()) {
                    $role->markShot();

                    return $game;
                }
            }

            return $game;
        }

        $user = $this->ensureUser($gameEvent);
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

        if (!$this->hunterSpec->canShoot($player, $game, $targetPlayer)) {
            throw new UnauthorizedGameActionException('You can not shoot');
        }

        $role = $player->getRole();
        if (!$role instanceof HunterRole) {
            throw new \LogicException(\sprintf('Role must be verified as a %s here', HunterRole::class));
        }

        $targetPlayer->setDead(true);
        $role->markShot();

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        if ($gameEvent instanceof HunterShootEvent) {
            return true;
        }

        return $gameEvent instanceof TimeUpGameEvent
            && GameRuntimeStepEnum::INTERUPT === $gameEvent->getGame()?->getRuntimeStep();
    }
}
