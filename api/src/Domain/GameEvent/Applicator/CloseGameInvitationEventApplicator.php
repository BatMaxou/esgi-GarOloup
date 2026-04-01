<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\GameSpec;
use App\Entity\Event\Game\CloseGameInvitationEvent;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Game\Game;
use App\Enum\Game\GameStepEnum;

/** @implements GameEventApplicatorInterface<CloseGameInvitationEvent> */
class CloseGameInvitationEventApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly GameSpec $gameSpec,
    ) {
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $user = $this->ensureUser($gameEvent);
        $game = $this->ensureGame($gameEvent);

        if (!$this->gameSpec->canCloseGameInvitation($user, $game)) {
            throw new UnauthorizedGameActionException('You can not close this game invitation');
        }

        return $game->setStep(GameStepEnum::CONFIGURATION);
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof CloseGameInvitationEvent;
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }
}
