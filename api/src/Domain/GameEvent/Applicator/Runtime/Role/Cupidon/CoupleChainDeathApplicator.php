<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Role\Cupidon;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Entity\Game\Role\LoverRole;
use App\Enum\Game\GameRoleEnum;
use App\Enum\TopicEnum;
use App\Service\Mercure\TopicCollector;
use App\Service\Mercure\TopicProvider;

/** @implements GameEventApplicatorInterface<TimeUpGameEvent> */
class CoupleChainDeathApplicator implements GameEventApplicatorInterface
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

        $partner = $this->findGrievingPartner($game);
        if (null === $partner) {
            return $game;
        }

        $partner->setDead(true);

        $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_PLAYER, $partner));

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        $game = $gameEvent->getGame();

        return $gameEvent instanceof TimeUpGameEvent
            && $game
            && $game->getPlayer(GameRoleEnum::CUPIDON);
    }

    private function findGrievingPartner(Game $game): ?Player
    {
        foreach ($game->getPlayers() as $player) {
            $role = $player->getRoleAs(LoverRole::class);
            if (!$role instanceof LoverRole || !$player->isDead()) {
                continue;
            }

            foreach ($game->getPlayers() as $candidate) {
                if ($candidate->getId()?->toString() === $role->getPartnerPlayerId() && !$candidate->isDead()) {
                    return $candidate;
                }
            }
        }

        return null;
    }
}
