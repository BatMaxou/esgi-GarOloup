<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Role\Cupidon;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Period\Interface\RevealedRoleActionInterface;
use App\Entity\Game\Player;
use App\Entity\Game\Role\LoverRole;
use App\Enum\Game\GameActionTypeEnum;
use App\Enum\Game\GameRoleEnum;
use App\Enum\TopicEnum;
use App\Service\Game\Period\PeriodActionFactory;
use App\Service\Mercure\TopicCollector;
use App\Service\Mercure\TopicProvider;

/** @implements GameEventApplicatorInterface<TimeUpGameEvent> */
class CoupleChainDeathApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;

    public function __construct(
        private readonly TopicCollector $topicCollector,
        private readonly TopicProvider $topicProvider,
        private readonly PeriodActionFactory $periodActionFactory,
    ) {
    }

    public static function getPriority(): int
    {
        return static::PRE_TRY_FINISH_PRIORITY;
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);

        $pair = $this->findGrievingPair($game);
        if (null === $pair) {
            return $game;
        }

        [$griever, $deceased] = $pair;

        $griever->setDead(true);
        $this->recordGriefDeath($game, $griever, $deceased);

        $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_PLAYER, $griever));

        return $game;
    }

    private function recordGriefDeath(Game $game, Player $griever, Player $deceased): void
    {
        $grieverId = $griever->getId()?->toString();
        $deceasedId = $deceased->getId()?->toString();
        if (null === $grieverId || null === $deceasedId) {
            return;
        }

        $action = $this->periodActionFactory->createForCurrentPeriod($game, GameActionTypeEnum::COUPLE_DEATH, $grieverId, $deceasedId);
        if ($action instanceof RevealedRoleActionInterface) {
            $action->setRevealedRole($griever->getRole()?->getType());
        }
    }

    public function supports(GameEvent $gameEvent): bool
    {
        $game = $gameEvent->getGame();

        return $gameEvent instanceof TimeUpGameEvent
            && $game
            && $game->getPlayer(GameRoleEnum::CUPIDON);
    }

    /**
     * @return array{0: Player, 1: Player}|null the grieving partner (still alive) and the lover already dead
     */
    private function findGrievingPair(Game $game): ?array
    {
        foreach ($game->getPlayers() as $player) {
            $role = $player->getRoleAs(LoverRole::class);
            if (!$role instanceof LoverRole || !$player->isDead()) {
                continue;
            }

            foreach ($game->getPlayers() as $candidate) {
                if ($candidate->getId()?->toString() === $role->getPartnerPlayerId() && !$candidate->isDead()) {
                    return [$candidate, $player];
                }
            }
        }

        return null;
    }
}
