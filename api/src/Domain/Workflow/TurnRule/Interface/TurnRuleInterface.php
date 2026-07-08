<?php

namespace App\Domain\Workflow\TurnRule\Interface;

use App\Entity\Game\Game;
use App\Enum\Game\GameRoleEnum;

interface TurnRuleInterface
{
    public function supports(GameRoleEnum $role): bool;

    public function shouldPlay(Game $game, GameRoleEnum $role): bool;
}
