<?php

namespace App\Api\Model\Game\Dispatch;

use App\Enum\Game\GameRoleEnum;

class RoleDispatchEntryInput
{
    public function __construct(
        public readonly string $playerId,
        public readonly GameRoleEnum $role,
    ) {
    }
}
