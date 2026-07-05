<?php

namespace App\Fixtures\Factory\Game\Role;

use App\Entity\Game\Role\LoverRole;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<LoverRole>
 */
final class LoverRoleFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return LoverRole::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [];
    }
}
