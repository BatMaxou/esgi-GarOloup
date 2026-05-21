<?php

namespace App\Fixtures\Factory\Game;

use App\Entity\Game\Workflow;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Workflow>
 */
final class WorkflowFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Workflow::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [];
    }
}
