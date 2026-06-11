<?php

namespace App\Domain\GameEvent\Applicator\Initialisation;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\AlreadyInAnotherGameException;
use App\Domain\GameEvent\Exception\UsernameAlreadyTakenException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\GameSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\JoinGameEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Entity\User\TempUser;
use Doctrine\ORM\EntityManagerInterface;

/** @implements GameEventApplicatorInterface<JoinGameEvent> */
class JoinGameEventApplicator implements GameEventApplicatorInterface
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

        if (!$this->gameSpec->canJoin($user, $game)) {
            throw new AlreadyInAnotherGameException('You are already playing a game');
        }

        if ($user instanceof TempUser) {
            if (!$this->gameSpec->isUsernameAvailable($user, $game)) {
                throw new UsernameAlreadyTakenException('This username is already taken');
            }
        } else {
            foreach ($game->getPlayers() as $player) {
                $username = $user->getUsername();
                if ($username === $player->getLinkedUser()->getUsername()) {
                    if (!$tempUser = $player->getTempUser()) {
                        throw new \LogicException('Player with same username of a valid user should be a temp user');
                    }

                    $counter = 2;
                    $alreadyTakenUsername = $tempUser->getUsername();
                    do {
                        $tempUser->setUsername(\sprintf('%s-%d', $alreadyTakenUsername, $counter++));
                    } while (!$this->gameSpec->isUsernameAvailable($tempUser, $game));
                }
            }
        }

        $player = new Player($user);
        $game->addPlayer($player);

        $this->em->persist($player);

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof JoinGameEvent;
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }
}
