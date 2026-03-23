<?php

namespace App\Fixtures\Factory;

use App\Entity\Role;
use App\Enum\Game\GameTeamEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Role>
 */
final class RoleFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Role::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->name(),
            'description' => self::faker()->text(100),
            'teams' => self::faker()->randomElements(GameTeamEnum::cases(), self::faker()->numberBetween(1, 2)),
        ];
    }
}
