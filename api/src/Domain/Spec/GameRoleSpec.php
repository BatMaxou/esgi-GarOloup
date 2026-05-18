<?php

namespace App\Domain\Spec;

use App\Entity\Game\Role\GameRole;
use App\Entity\Game\Role\VillagerRole;
use App\Entity\Game\Role\WerewolfRole;
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
            default => null,
        })?->setRole($role);
    }
}
