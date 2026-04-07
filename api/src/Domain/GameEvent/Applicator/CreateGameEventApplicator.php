<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\AlreadyInAnotherGameException;
use App\Domain\GameEvent\Exception\InvalidConfigurationException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\GameSpec;
use App\Entity\Event\Game\CreateGameEvent;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Player;
use Doctrine\ORM\EntityManagerInterface;

/** @implements GameEventApplicatorInterface<CreateGameEvent> */
class CreateGameEventApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly GameSpec $gameSpec,
        private readonly EntityManagerInterface $em,
        private readonly int $minimumPlayers,
    ) {
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $user = $this->ensureUser($gameEvent);
        if (!$this->gameSpec->canCreate($user)) {
            throw new AlreadyInAnotherGameException('You are already playing a game');
        }

        $maxPlayers = $gameEvent->getMaxPlayers();
        $maxTimeForDiscussion = $gameEvent->getMaxTimeForDiscussion();
        if ($maxPlayers < $this->minimumPlayers || $maxTimeForDiscussion < 1) {
            throw new InvalidConfigurationException('Invalid game configuration');
        }

        $player = new Player($user);
        $game = new Game($player)
            ->setMaxPlayers($maxPlayers)
            ->setMaxTimeForDiscussion($maxTimeForDiscussion)
            ->setPublic($gameEvent->isPublic());

        $this->em->persist($player);
        $this->em->persist($game);

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof CreateGameEvent;
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }
}
