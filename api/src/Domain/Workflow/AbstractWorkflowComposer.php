<?php

namespace App\Domain\Workflow;

use App\Entity\Game\Game;
use App\Entity\Game\Role\RoleEntry;
use App\Entity\Game\Workflow;
use App\Enum\Game\GameRoleEnum;

abstract class AbstractWorkflowComposer
{
    public function for(Game $game): Workflow
    {
        $entries = $game->getConfiguration()->getComposition()?->getRoles()->toArray() ?? [];

        $playable = \array_filter(
            $entries,
            fn (RoleEntry $entry) => null !== $this->getPriority($entry->getRole()->getType()),
        );

        $byPriority = [];
        foreach ($playable as $entry) {
            $type = $entry->getRole()->getType();
            if (null === $type) {
                continue;
            }

            $priority = $this->getPriority($type);
            if (null === $priority) {
                continue;
            }

            $byPriority[$priority][$type->value] = $type;
        }
        \ksort($byPriority);

        $workflow = new Workflow();
        foreach ($byPriority as $group) {
            $workflow->addStep(...$group);
        }

        return $workflow;
    }

    abstract protected function getPriority(?GameRoleEnum $role): ?int;
}
