<?php

namespace App\Fixtures\Factory\Game\Role;

use App\Entity\Game\Role\CupidonRole;
use App\Enum\Game\GameRoleEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<CupidonRole>
 */
final class CupidonRoleFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return CupidonRole::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'role' => null,
            'type' => GameRoleEnum::CUPIDON,
        ];
    }
}
