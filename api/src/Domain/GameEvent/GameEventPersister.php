<?php

namespace App\Domain\GameEvent;

use App\Domain\GameEvent\Interface\GameEventPersisterInterface;
use App\Entity\Event\Game\GameEvent;
use Doctrine\ORM\EntityManagerInterface;

class GameEventPersister implements GameEventPersisterInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function persist(GameEvent $gameEvent): void
    {
        if (!$gameEvent->getGameId()) {
            $game = $gameEvent->getGame();
            $gameId = $game?->getId();
            if (!$game || !$gameId) {
                throw new \RuntimeException('Game is required');
            }

            $gameEvent->setGameId($gameId);
        }

        if (!$gameEvent->getPlayerUsername()) {
            $user = $gameEvent->getUser();
            if (!$user) {
                throw new \RuntimeException('User is required');
            }

            $gameEvent->setPlayerUsername($user->getUsername());
        }

        $this->em->persist($gameEvent);
    }
}
