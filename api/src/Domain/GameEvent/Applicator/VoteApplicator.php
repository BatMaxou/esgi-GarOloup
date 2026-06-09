<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\GameSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\VoteEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Period\Vote\Ballot;
use App\Repository\Game\PlayerRepository;
use Symfony\Component\Uid\Uuid;

/**
 * @implements GameEventApplicatorInterface<VoteEvent>
 */
class VoteApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly GameSpec $gameSpec,
        private readonly PlayerRepository $playerRepository,
    ) {
    }

    public static function getPriority(): int
    {
        return self::DEFAULT_PRIORITY;
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $user = $this->ensureUser($gameEvent);
        $game = $this->ensureGame($gameEvent);

        $voter = $this->playerRepository->findCurrentByUser($user);
        if (null === $voter) {
            throw new PlayerNotFoundException('Current player not found');
        }

        $targetPlayerId = $gameEvent->getTargetPlayerId();
        if (!Uuid::isValid($targetPlayerId)) {
            throw new PlayerNotFoundException('Target player not found');
        }

        $target = $this->playerRepository->find($targetPlayerId);
        if (null === $target || $game !== $target->getGame()) {
            throw new PlayerNotFoundException('Target player not found');
        }

        if (!$this->gameSpec->canVote($voter, $game, $target)) {
            throw new UnauthorizedGameActionException('You can not vote');
        }

        $vote = $game->getCurrentVote();
        if (null === $vote) {
            throw new UnauthorizedGameActionException('No active vote');
        }

        $alreadyVoted = false;
        foreach ($vote->getBallots() as $ballot) {
            if ($ballot->getPlayer() === $voter) {
                $ballot->setTarget($target);
                $alreadyVoted = true;
            }
        }

        if (!$alreadyVoted) {
            $vote->addBallot(new Ballot($vote, $voter, $target));
        }

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof VoteEvent;
    }
}
