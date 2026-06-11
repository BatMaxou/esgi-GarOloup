<?php

namespace App\Domain\GameEvent\Applicator\Initialisation;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\GameSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\ResetGameMasterEvent;
use App\Entity\Game\Game;
use App\Enum\Game\GameInitialisationStepEnum;

/** @implements GameEventApplicatorInterface<ResetGameMasterEvent> */
class ResetGameMasterEventApplicator implements GameEventApplicatorInterface
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

        if (!$this->gameSpec->canResetGameMaster($user, $game)) {
            throw new UnauthorizedGameActionException('You can not reset the game master of this game');
        }

        return $game
            ->removeGameMaster()
            ->setInitialisationStep(GameInitialisationStepEnum::GAME_MASTER_CHOICE);
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof ResetGameMasterEvent;
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }
}
