<?php

namespace App\Entity\Game\Role\Interface;

use App\Enum\Game\GameRoleEnum;

interface NightKillImmuneInterface
{
    public function isImmuneToNightMurder(GameRoleEnum $source): bool;
}
