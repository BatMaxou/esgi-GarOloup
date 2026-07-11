<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Role\InfectFather;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\NightAction\InfectAction;
use App\Entity\Game\Period\Night;
use App\Entity\Game\Player;
use App\Entity\Game\Role\InfectedRole;
use App\Entity\Game\Role\LoverRole;
use App\Enum\Game\GameTeamEnum;
use App\Enum\TopicEnum;
use App\Service\Game\WerewolfTeamFactory;
use App\Service\Mercure\TopicCollector;
use App\Service\Mercure\TopicProvider;
use Doctrine\ORM\EntityManagerInterface;

/** @implements GameEventApplicatorInterface<TimeUpGameEvent> */
class InfectTransformApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;

    public function __construct(
        private readonly TopicCollector $topicCollector,
        private readonly TopicProvider $topicProvider,
        private readonly EntityManagerInterface $em,
        private readonly WerewolfTeamFactory $werewolfTeamFactory,
    ) {
    }

    public static function getPriority(): int
    {
        return static::PRE_TRY_FINISH_PRIORITY;
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);

        $night = $game->getNights()->last();
        if (!$night instanceof Night || !$night->isResolved()) {
            return $game;
        }

        $victim = $this->findInfectedVictim($game);
        if (null === $victim || $victim->isDead()) {
            return $game;
        }

        $originalRole = $victim->getRole();
        if (null === $originalRole) {
            return $game;
        }

        $loverRole = $victim->getRoleAs(LoverRole::class);
        if ($loverRole instanceof LoverRole) {
            $infectedRole = new InfectedRole($loverRole->getOriginalRole());
            $this->em->persist($infectedRole);
            $loverRole->setOriginalRole($infectedRole);
        } else {
            $infectedRole = new InfectedRole($originalRole);
            $this->em->persist($infectedRole);
            $victim->setRole($infectedRole);
        }

        $victim->setTeam(GameTeamEnum::WEREWOLF);

        $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_PLAYER, $victim));

        $werewolfTeam = $this->werewolfTeamFactory->fromGame($game);
        if (null !== $werewolfTeam) {
            $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::WEREWOLF_TEAM, $werewolfTeam));
        }

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof TimeUpGameEvent
            && null !== $this->findInfectedVictim($gameEvent->getGame());
    }

    private function findInfectedVictim(?Game $game): ?Player
    {
        $night = $game?->getNights()->last();
        if (null === $game || !$night instanceof Night) {
            return null;
        }

        foreach ($night->getActions() as $action) {
            if (!$action instanceof InfectAction) {
                continue;
            }

            foreach ($game->getPlayers() as $player) {
                if ((string) $player->getId() !== $action->getTargetPlayerId()) {
                    continue;
                }

                if ($player->getRoleAs(InfectedRole::class)) {
                    return null;
                }

                return $player;
            }
        }

        return null;
    }
}
