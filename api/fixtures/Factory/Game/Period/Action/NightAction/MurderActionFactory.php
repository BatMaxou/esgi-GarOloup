<?php

namespace App\Fixtures\Factory\Game\Period\Action\NightAction;

use App\Entity\Game\Period\Action\NightAction\MurderAction;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<MurderAction>
 */
final class MurderActionFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return MurderAction::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [];
    }
}
