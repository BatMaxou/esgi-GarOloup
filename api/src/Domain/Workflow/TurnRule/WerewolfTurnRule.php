<?php

namespace App\Domain\Workflow\TurnRule;

use App\Domain\Workflow\TurnRule\Interface\TurnRuleInterface;
use App\Entity\Game\Game;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameTeamEnum;

class WerewolfTurnRule implements TurnRuleInterface
{
    public function supports(GameRoleEnum $role): bool
    {
        return GameRoleEnum::WEREWOLF === $role;
    }

    public function shouldPlay(Game $game, GameRoleEnum $role): bool
    {
        foreach ($game->getPlayers() as $player) {
            if (!$player->isDead() && GameTeamEnum::WEREWOLF === $player->getTeam()) {
                return true;
            }
        }

        return false;
    }
}
