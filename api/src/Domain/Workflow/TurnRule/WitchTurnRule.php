<?php

namespace App\Domain\Workflow\TurnRule;

use App\Domain\Workflow\TurnRule\Interface\TurnRuleInterface;
use App\Entity\Game\Game;
use App\Entity\Game\Role\WitchRole;
use App\Enum\Game\GameRoleEnum;

class WitchTurnRule implements TurnRuleInterface
{
    public function supports(GameRoleEnum $role): bool
    {
        return GameRoleEnum::WITCH === $role;
    }

    public function shouldPlay(Game $game, GameRoleEnum $role): bool
    {
        $player = $game->getPlayer($role);
        if (null === $player || $player->isDead()) {
            return false;
        }

        $witchRole = $player->getRoleAs(WitchRole::class);
        if (null === $witchRole) {
            return false;
        }

        return $witchRole->isHealPotionAvailable() || $witchRole->isPoisonPotionAvailable();
    }
}
