<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Exception\AlreadyInAnotherGameException;
use App\Domain\GameEvent\Applicator\Interface\GameEventApplicatorInterface;
use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\Spec\GameSpec;
use App\Entity\Event\Game\CreateGameEvent;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Game;
use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;

/** @implements GameEventApplicatorInterface<CreateGameEvent> */
class CreateGameEventApplicator implements GameEventApplicatorInterface
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
        if (!$this->gameSpec->canCreate($user)) {
            throw new AlreadyInAnotherGameException('You are already playing a game');
        }

        $player = new Player($user);
        $game = new Game($player);

        $this->em->persist($player);
        $this->em->persist($game);
        $this->em->flush();

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
