<?php

namespace App\Fixtures\Factory\Game;

use App\Entity\Game\Role\RoleEntry;
use App\Fixtures\Factory\RoleFactory;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<RoleEntry>
 */
final class RoleEntryFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return RoleEntry::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'composition' => CompositionFactory::new(),
            'role' => RoleFactory::new(),
            'count' => self::faker()->numberBetween(1, 5),
        ];
    }
}
