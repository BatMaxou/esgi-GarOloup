<?php

namespace App\Fixtures\Factory\Game\Role;

use App\Entity\Game\Role\WerewolfRole;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<WerewolfRole>
 */
final class WerewolfRoleFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return WerewolfRole::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'role' => null,
        ];
    }
}
