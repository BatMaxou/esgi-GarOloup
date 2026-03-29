<?php

namespace App\Fixtures\Factory\Game;

use App\Entity\Game\Composition;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Composition>
 */
final class CompositionFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Composition::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'roles' => [],
        ];
    }
}
