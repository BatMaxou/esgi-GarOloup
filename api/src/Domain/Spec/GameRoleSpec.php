<?php

namespace App\Domain\Spec;

use App\Entity\Game\Role\CupidonRole;
use App\Entity\Game\Role\GameRole;
use App\Entity\Game\Role\HunterRole;
use App\Entity\Game\Role\InfectFatherRole;
use App\Entity\Game\Role\SeerRole;
use App\Entity\Game\Role\VillagerRole;
use App\Entity\Game\Role\WerewolfRole;
use App\Entity\Game\Role\WildChildRole;
use App\Entity\Game\Role\WitchRole;
use App\Entity\Role;
use App\Enum\Game\GameRoleEnum;

class GameRoleSpec
{
    public function getAssociatedGameRole(Role $role): ?GameRole
    {
        $type = $role->getType();
        if (!$type) {
            return null;
        }

        return (match ($type) {
            GameRoleEnum::VILLAGER => new VillagerRole(),
            GameRoleEnum::WEREWOLF => new WerewolfRole(),
            GameRoleEnum::SEER => new SeerRole(),
            GameRoleEnum::WITCH => new WitchRole(),
            GameRoleEnum::WILD_CHILD => new WildChildRole(),
            GameRoleEnum::HUNTER => new HunterRole(),
            GameRoleEnum::INFECT_FATHER => new InfectFatherRole(),
            GameRoleEnum::CUPIDON => new CupidonRole(),
            default => null,
        })?->setRole($role);
    }
}
