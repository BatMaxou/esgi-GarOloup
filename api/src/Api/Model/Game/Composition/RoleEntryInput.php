<?php

namespace App\Api\Model\Game\Composition;

use App\Enum\Game\GameRoleEnum;

class RoleEntryInput
{
    public function __construct(
        public readonly GameRoleEnum $role,
        public readonly int $count,
    ) {
    }
}
