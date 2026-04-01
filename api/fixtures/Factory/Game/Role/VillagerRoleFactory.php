<?php

namespace App\Fixtures\Factory\Game\Role;

use App\Entity\Game\Role\VillagerRole;
use App\Enum\Game\GameRoleEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<VillagerRole>
 */
final class VillagerRoleFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return VillagerRole::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'role' => null,
            'type' => GameRoleEnum::VILLAGER,
        ];
    }
}
