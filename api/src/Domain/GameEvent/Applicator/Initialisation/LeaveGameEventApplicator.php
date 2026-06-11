<?php

namespace App\Domain\GameEvent\Applicator\Initialisation;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\GameSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\LeaveGameEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Player;
use Doctrine\ORM\EntityManagerInterface;

/** @implements GameEventApplicatorInterface<LeaveGameEvent> */
class LeaveGameEventApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly GameSpec $gameSpec,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $user = $this->ensureUser($gameEvent);
        $game = $this->ensureGame($gameEvent);

        if (!$this->gameSpec->canLeave($user, $game)) {
            throw new UnauthorizedGameActionException('You can not leave this game');
        }

        $leavingPlayer = null;
        foreach ($game->getPlayers() as $player) {
            if ($player->getLinkedUser() === $user) {
                $leavingPlayer = $player;

                continue;
            }
        }

        if (!$leavingPlayer instanceof Player) {
            throw new PlayerNotFoundException('You do not have a player in this game');
        }

        $game->removePlayer($leavingPlayer);
        if ($game->getPlayers()->isEmpty()) {
            return $game;
        }

        if ($game->getHost() === $leavingPlayer) {
            $others = \array_values($game->getPlayers()->toArray());
            $game->setHost($others[\array_rand($others)]);
        }

        $this->em->remove($leavingPlayer);

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof LeaveGameEvent;
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }
}
