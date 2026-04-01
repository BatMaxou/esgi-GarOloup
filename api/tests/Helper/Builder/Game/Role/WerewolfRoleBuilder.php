<?php

namespace App\Tests\Helper\Builder\Role;

use App\Entity\Game\Role\WerewolfRole;
use App\Fixtures\Factory\Game\Role\WerewolfRoleFactory;

/** @extends GameRoleBuilder<WerewolfRole> */
abstract class WerewolfRoleBuilder extends GameRoleBuilder
{
    protected function doBuild(): object
    {
        return WerewolfRoleFactory::createOne([
            ...($this->role ? ['role' => $this->role] : []),
        ]);
    }
}
