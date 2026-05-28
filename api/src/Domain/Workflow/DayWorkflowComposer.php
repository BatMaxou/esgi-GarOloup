<?php

namespace App\Domain\Workflow;

use App\Enum\Game\GameRoleEnum;

class DayWorkflowComposer extends AbstractWorkflowComposer
{
    public function getPriority(?GameRoleEnum $role): ?int
    {
        return $role?->getDayPriority();
    }
}
