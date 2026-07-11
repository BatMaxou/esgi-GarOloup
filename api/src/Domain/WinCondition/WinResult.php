<?php

namespace App\Domain\WinCondition;

use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameTeamEnum;

final readonly class WinResult
{
    public function __construct(
        public GameTeamEnum $team,
        public ?GameRoleEnum $winningRole = null,
    ) {
    }
}
