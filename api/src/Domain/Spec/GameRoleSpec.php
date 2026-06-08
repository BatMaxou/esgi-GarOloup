<?php

namespace App\Domain\Spec;

use App\Entity\Game\Role\GameRole;
use App\Entity\Game\Role\SeerRole;
use App\Entity\Game\Role\VillagerRole;
use App\Entity\Game\Role\WerewolfRole;
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
            default => null,
        })?->setRole($role);
    }
}
