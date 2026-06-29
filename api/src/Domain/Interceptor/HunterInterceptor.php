<?php

namespace App\Domain\Interceptor;

use App\Domain\Interceptor\Interface\InterceptorInterface;
use App\Entity\Game\Game;
use App\Entity\Game\Role\HunterRole;
use App\Enum\Game\GameRoleEnum;

class HunterInterceptor implements InterceptorInterface
{
    public function hasPendingAction(Game $game): bool
    {
        foreach ($game->getPlayers() as $player) {
            $role = $player->getRole();
            if ($player->isDead() && $role instanceof HunterRole && !$role->hasShot()) {
                return true;
            }
        }

        return false;
    }

    public function getRole(): GameRoleEnum
    {
        return GameRoleEnum::HUNTER;
    }
}
