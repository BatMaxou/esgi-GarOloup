<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Role\Seer;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\Role\SeerSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\SeerRevealEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Role\SeerRole;
use App\Enum\TopicEnum;
use App\Repository\Game\PlayerRepository;
use App\Service\Mercure\TopicCollector;
use App\Service\Mercure\TopicProvider;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

/** @implements GameEventApplicatorInterface<SeerRevealEvent> */
class SeerRevealApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly SeerSpec $seerSpec,
        private readonly PlayerRepository $playerRepository,
        private readonly ClockInterface $clock,
        private readonly TopicProvider $topicProvider,
        private readonly TopicCollector $topicCollector,
    ) {
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }

    public function apply(GameEvent $gameEvent): Game
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

        $role = $player->getRoleAs(SeerRole::class);
        if (null === $role) {
            throw new \LogicException(\sprintf('Role must be verified as a %s here', SeerRole::class));
        }

        $role->observe($targetPlayer);

        $game->setStepEndAt($this->clock->now());
        $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_GAME, $game));

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof SeerRevealEvent;
    }
}
