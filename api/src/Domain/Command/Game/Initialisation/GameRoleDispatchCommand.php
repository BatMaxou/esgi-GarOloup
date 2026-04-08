<?php

namespace App\Domain\Command\Game\Initialisation;

use App\Api\Model\Game\Dispatch\RoleDispatchEntryInput;

class GameRoleDispatchCommand
{
    /**
     * @param RoleDispatchEntryInput[] $dispatch
     */
    public function __construct(
        public readonly array $dispatch = [],
    ) {
    }
}
