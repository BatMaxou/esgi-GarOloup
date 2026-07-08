<?php

namespace App\Domain\Spec\Role;

use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Entity\Game\Role\AssassinRole;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use Symfony\Component\Clock\ClockInterface;

class AssassinSpec
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function canKill(Player $assassin, Game $game, Player $target): bool
    {
        if (!$this->isAssassinTurn($assassin, $game)) {
            return false;
        }

        $night = $game->getCurrentNight();
        if (null === $night || $night->getNumber() <= 1) {
            return false;
        }

        $role = $assassin->getRoleAs(AssassinRole::class);
        if (!$role instanceof AssassinRole || $role->hasActedThisNight()) {
            return false;
        }

        if ($assassin->getId()?->toString() === $target->getId()?->toString()) {
            return false;
        }

        if ($target->getGame() !== $game || $target->isDead()) {
            return false;
        }

        return true;
    }

    private function isAssassinTurn(Player $assassin, Game $game): bool
    {
        if (GameRuntimeStepEnum::NIGHT !== $game->getRuntimeStep()) {
            return false;
        }

        if ($game->getStepEndAt() < $this->clock->now()) {
            return false;
        }

        if ($assassin->isDead()) {
            return false;
        }

        $workflow = $game->getNightWorkflow();

        return null !== $workflow && \in_array(GameRoleEnum::ASSASSIN, $workflow->getCurrentTurn(), true);
    }
}
