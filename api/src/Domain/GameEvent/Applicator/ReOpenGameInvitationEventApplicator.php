<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\GameSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\ReOpenGameInvitationEvent;
use App\Entity\Game\Game;
use App\Enum\Game\GameStepEnum;
use Doctrine\ORM\EntityManagerInterface;

/** @implements GameEventApplicatorInterface<ReOpenGameInvitationEvent> */
class ReOpenGameInvitationEventApplicator implements GameEventApplicatorInterface
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

        if (!$this->gameSpec->canReOpenGameInvitation($user, $game)) {
            throw new UnauthorizedGameActionException('You can not open invitation for this game');
        }

        $game->setStep(GameStepEnum::NEW);

        $this->em->flush();

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof ReOpenGameInvitationEvent;
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }
}
