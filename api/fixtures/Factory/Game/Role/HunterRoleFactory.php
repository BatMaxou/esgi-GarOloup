<?php

namespace App\Fixtures\Factory\Game\Role;

use App\Entity\Game\Role\HunterRole;
use App\Enum\Game\GameRoleEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<HunterRole>
 */
final class HunterRoleFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return HunterRole::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'role' => null,
            'type' => GameRoleEnum::HUNTER,
        ];
    }
}
