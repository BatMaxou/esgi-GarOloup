<?php

namespace App\Fixtures\Factory\Game\Period\Action\NightAction;

use App\Entity\Game\Period\Action\NightAction\InfectAction;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<InfectAction>
 */
final class InfectActionFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return InfectAction::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [];
    }
}
