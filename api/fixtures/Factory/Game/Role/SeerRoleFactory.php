<?php

namespace App\Fixtures\Factory\Game\Role;

use App\Entity\Game\Role\SeerRole;
use App\Enum\Game\GameRoleEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<SeerRole>
 */
final class SeerRoleFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return SeerRole::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'role' => null,
            'type' => GameRoleEnum::SEER,
        ];
    }
}
