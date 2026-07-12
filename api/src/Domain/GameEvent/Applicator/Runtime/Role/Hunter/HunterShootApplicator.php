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
use App\Entity\Game\Period\Interface\RevealedRoleActionInterface;
use App\Entity\Game\Role\HunterRole;
use App\Enum\Game\GameActionTypeEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\TopicEnum;
use App\Repository\Game\PlayerRepository;
use App\Service\Game\Period\PeriodActionFactory;
use App\Service\Mercure\TopicCollector;
use App\Service\Mercure\TopicProvider;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

/** @implements GameEventApplicatorInterface<HunterShootEvent|TimeUpGameEvent> */
class HunterShootApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly HunterSpec $hunterSpec,
        private readonly PlayerRepository $playerRepository,
        private readonly ClockInterface $clock,
        private readonly TopicProvider $topicProvider,
        private readonly TopicCollector $topicCollector,
        private readonly PeriodActionFactory $periodActionFactory,
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
                $role = $player->getRoleAs(HunterRole::class);
                if ($player->isDead() && null !== $role && !$role->hasShot()) {
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

        $role = $player->getRoleAs(HunterRole::class);
        if (null === $role) {
            throw new \LogicException(\sprintf('Role must be verified as a %s here', HunterRole::class));
        }

        $targetPlayer->setDead(true);
        $role->markShot();
        $action = $this->periodActionFactory->createForCurrentPeriod($game, GameActionTypeEnum::HUNTER_SHOT, $targetPlayerId);
        if ($action instanceof RevealedRoleActionInterface) {
            $action->setRevealedRole($targetPlayer->getRole()?->getType());
        }

        $game->setStepEndAt($this->clock->now());
        $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_GAME, $game));

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        if ($gameEvent instanceof HunterShootEvent) {
            return true;
        }

        return $gameEvent instanceof TimeUpGameEvent
            && GameRuntimeStepEnum::INTERRUPT === $gameEvent->getGame()?->getRuntimeStep();
    }
}
