<?php

namespace App\Domain\Workflow;

use App\Entity\Game\Game;
use App\Entity\Game\RoleEntry;
use App\Entity\Game\Workflow;

class WorkflowBuilder
{
    public function buildFor(Game $game): void
    {
        $entries = $game->getConfiguration()->getComposition()?->getRoles()->toArray() ?? [];

        $playableAtNight = \array_filter(
            $entries,
            fn (RoleEntry $entry) => null !== $entry->getRole()->getType()?->getPriority(),
        );

        $byPriority = [];
        foreach ($playableAtNight as $entry) {
            $type = $entry->getRole()->getType();
            if (null === $type) {
                continue;
            }

            $byPriority[$type->getPriority()][$type->value] = $type;
        }
        \ksort($byPriority);

        $workflow = new Workflow();
        foreach ($byPriority as $group) {
            $workflow->addStep(...$group);
        }

        $game->setWorkflow($workflow);
    }
}
