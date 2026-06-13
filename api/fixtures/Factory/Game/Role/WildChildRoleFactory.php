<?php

namespace App\Fixtures\Factory\Game\Role;

use App\Entity\Game\Role\WildChildRole;
use App\Enum\Game\GameRoleEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<WildChildRole>
 */
final class WildChildRoleFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return WildChildRole::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'role' => null,
            'type' => GameRoleEnum::WILD_CHILD,
        ];
    }
}
