<?php

namespace App\Domain\Workflow\TurnRule;

use App\Domain\Workflow\TurnRule\Interface\TurnRuleInterface;
use App\Entity\Game\Game;
use App\Entity\Game\Role\InfectFatherRole;
use App\Enum\Game\GameRoleEnum;

class InfectFatherTurnRule implements TurnRuleInterface
{
    public function supports(GameRoleEnum $role): bool
    {
        return GameRoleEnum::INFECT_FATHER === $role;
    }

    public function shouldPlay(Game $game, GameRoleEnum $role): bool
    {
        $player = $game->getPlayer($role);
        if (null === $player || $player->isDead()) {
            return false;
        }

        $infectFatherRole = $player->getRoleAs(InfectFatherRole::class);
        if (null === $infectFatherRole) {
            return false;
        }

        return $infectFatherRole->isInfectionAvailable();
    }
}
