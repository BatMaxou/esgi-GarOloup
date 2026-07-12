<?php

namespace App\Entity\Game\Period\Interface;

use App\Enum\Game\GameRoleEnum;

interface SeenRoleActionInterface
{
    public function getSeenRole(): ?GameRoleEnum;
}
