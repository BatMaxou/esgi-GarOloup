<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Role\WildChild;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Role\WildChildRole;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameTeamEnum;
use App\Enum\TopicEnum;
use App\Service\Mercure\TopicCollector;
use App\Service\Mercure\TopicProvider;

/** @implements GameEventApplicatorInterface<TimeUpGameEvent> */
class WildChildApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;

    public function __construct(
        private readonly TopicCollector $topicCollector,
        private readonly TopicProvider $topicProvider,
    ) {
    }

    public static function getPriority(): int
    {
        return static::PRE_TRY_FINISH_PRIORITY;
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);
        $player = $game->getPlayer(GameRoleEnum::WILD_CHILD);
        $role = $player?->getRoleAs(WildChildRole::class);
        if (!$player || !$role instanceof WildChildRole) {
            return $game;
        }

        foreach ($game->getPlayers() as $otherPlayer) {
            if ($otherPlayer->getId()?->toString() === $role->getModelPlayerId() && $otherPlayer->isDead()) {
                $role->transform();
                $player->setTeam(GameTeamEnum::WEREWOLF);

                $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_PLAYER, $player));
                $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::WEREWOLF_TEAM, $game));

                break;
            }
        }

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        $player = $gameEvent->getGame()?->getPlayer(GameRoleEnum::WILD_CHILD);
        $role = $player?->getRoleAs(WildChildRole::class);

        return $player && !$player->isDead() && $role instanceof WildChildRole && !$role->isTransformed();
    }
}
