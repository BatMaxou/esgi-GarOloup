<?php

namespace App\Fixtures\Factory\Game\Role;

use App\Entity\Game\Role\AssassinRole;
use App\Enum\Game\GameRoleEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<AssassinRole>
 */
final class AssassinRoleFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return AssassinRole::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'role' => null,
            'type' => GameRoleEnum::ASSASSIN,
        ];
    }
}
