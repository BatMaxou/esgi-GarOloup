<?php

namespace App\Fixtures\Factory\Game\Role;

use App\Entity\Game\Role\WitchRole;
use App\Enum\Game\GameRoleEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<WitchRole>
 */
final class WitchRoleFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return WitchRole::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'role' => null,
            'type' => GameRoleEnum::WITCH,
        ];
    }
}
