<?php

namespace App\Fixtures\Factory\Game;

use App\Entity\Game\Configuration;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Configuration>
 */
final class ConfigurationFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Configuration::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'composition' => CompositionFactory::new(),
        ];
    }
}
