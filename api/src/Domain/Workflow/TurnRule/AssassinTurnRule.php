<?php

namespace App\Domain\Workflow\TurnRule;

use App\Domain\Workflow\TurnRule\Interface\TurnRuleInterface;
use App\Entity\Game\Game;
use App\Enum\Game\GameRoleEnum;

class AssassinTurnRule implements TurnRuleInterface
{
    public function supports(GameRoleEnum $role): bool
    {
        return GameRoleEnum::ASSASSIN === $role;
    }

    public function shouldPlay(Game $game, GameRoleEnum $role): bool
    {
        $player = $game->getPlayer($role);
        if (null === $player || $player->isDead()) {
            return false;
        }

        $night = $game->getCurrentNight();

        return null !== $night && $night->getNumber() > 1;
    }
}
