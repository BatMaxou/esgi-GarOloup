<?php

namespace App\Domain\Spec\Role;

use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Entity\Game\Role\SeerRole;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use Symfony\Component\Clock\ClockInterface;

class SeerSpec
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function canReveal(Player $seer, Game $game, Player $target): bool
    {
        if (GameRuntimeStepEnum::NIGHT !== $game->getRuntimeStep()) {
            return false;
        }

        if ($game->getStepEndAt() < $this->clock->now()) {
            return false;
        }

        $workflow = $game->getNightWorkflow();
        if (null === $workflow || !\in_array(GameRoleEnum::SEER, $workflow->getCurrentTurn(), true)) {
            return false;
        }

        $role = $seer->getRole();
        if (!$role instanceof SeerRole || $seer->isDead()) {
            return false;
        }

        if (null !== $role->getLastObservedPlayerId()) {
            return false;
        }

        if ($seer->getId()?->toString() === $target->getId()?->toString()) {
            return false;
        }

        if ($target->getGame() !== $game || $target->isDead()) {
            return false;
        }

        return true;
    }
}
