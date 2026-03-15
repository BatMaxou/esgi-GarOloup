<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Applicator\Interface\GameEventApplicatorInterface;
use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\Spec\GameSpec;
use App\Entity\Event\Game\CloseInvitationEvent;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Game;
use App\Enum\GameStepEnum;
use Doctrine\ORM\EntityManagerInterface;

/** @implements GameEventApplicatorInterface<CloseInvitationEvent> */
class CloseInvitationEventApplicator implements GameEventApplicatorInterface
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

        if (!$this->gameSpec->canCloseGameInvitation($user, $game)) {
            throw new UnauthorizedGameActionException('You can not close this game invitation');
        }

        $game->setStep(GameStepEnum::CONFIGURATION);

        $this->em->flush();

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof CloseInvitationEvent;
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }
}
