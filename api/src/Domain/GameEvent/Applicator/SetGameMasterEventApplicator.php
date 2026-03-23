<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\GameSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\SetGameMasterEvent;
use App\Entity\Game\Game;
use App\Repository\Game\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;

/** @implements GameEventApplicatorInterface<SetGameMasterEvent> */
class SetGameMasterEventApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly GameSpec $gameSpec,
        private readonly EntityManagerInterface $em,
        private readonly PlayerRepository $playerRepository,
    ) {
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $user = $this->ensureUser($gameEvent);
        $game = $this->ensureGame($gameEvent);

        if (!$this->gameSpec->canSetGameMaster($user, $game)) {
            throw new UnauthorizedGameActionException('You can not set the game master');
        }

        try {
            $targetPlayer = $this->playerRepository->find($gameEvent->getTargetPlayerId());
        } catch (\Throwable $e) {
            throw new PlayerNotFoundException('Invalid player ID');
        }

        if (null === $targetPlayer || $targetPlayer->getGame() !== $game) {
            throw new PlayerNotFoundException('Target player not found in this game');
        }

        $game->setGameMaster($targetPlayer);

        $this->em->flush();

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof SetGameMasterEvent;
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }
}
