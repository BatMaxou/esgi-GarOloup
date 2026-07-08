<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Role\Assassin;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\Role\AssassinSpec;
use App\Entity\Event\Game\AssassinKillEvent;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\NightAction\MurderAction;
use App\Entity\Game\Role\AssassinRole;
use App\Enum\Game\GameRoleEnum;
use App\Enum\TopicEnum;
use App\Repository\Game\PlayerRepository;
use App\Service\Mercure\TopicCollector;
use App\Service\Mercure\TopicProvider;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

/** @implements GameEventApplicatorInterface<AssassinKillEvent> */
class AssassinKillApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly AssassinSpec $assassinSpec,
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

        if (!$this->assassinSpec->canKill($player, $game, $targetPlayer)) {
            throw new UnauthorizedGameActionException('You can not kill');
        }

        $role = $player->getRoleAs(AssassinRole::class);
        if (null === $role) {
            throw new \LogicException(\sprintf('Role must be verified as a %s here', AssassinRole::class));
        }

        $night = $game->getCurrentNight() ?? throw new \LogicException('No active night to register the assassin action');

        $night->addAction(new MurderAction($night, GameRoleEnum::ASSASSIN, $targetPlayerId));
        $role->markActedThisNight();

        $game->setStepEndAt($this->clock->now());
        $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_GAME, $game));

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof AssassinKillEvent;
    }
}
