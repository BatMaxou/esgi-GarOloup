<?php

namespace App\Domain\Interceptor\Interface;

use App\Entity\Game\Game;
use App\Enum\Game\GameRoleEnum;

interface InterceptorInterface
{
    public function hasPendingAction(Game $game): bool;

    public function getRole(): GameRoleEnum;
}
