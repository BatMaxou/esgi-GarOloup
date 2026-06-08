<?php

namespace App\Fixtures\Factory\Game\Period\Action\NightAction;

use App\Entity\Game\Period\Action\NightAction\SaveAction;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<SaveAction>
 */
final class SaveActionFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return SaveAction::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [];
    }
}
