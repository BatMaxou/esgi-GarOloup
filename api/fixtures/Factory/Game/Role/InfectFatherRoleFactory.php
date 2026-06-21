<?php

namespace App\Fixtures\Factory\Game\Role;

use App\Entity\Game\Role\InfectFatherRole;
use App\Enum\Game\GameRoleEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<InfectFatherRole>
 */
final class InfectFatherRoleFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return InfectFatherRole::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'role' => null,
            'type' => GameRoleEnum::INFECT_FATHER,
        ];
    }
}
