<?php

namespace App\Tests\Helper\Builder\Game\Role;

use App\Entity\Game\Role\WerewolfRole;
use App\Fixtures\Factory\Game\Role\WerewolfRoleFactory;

/** @extends GameRoleBuilder<WerewolfRole> */
class WerewolfRoleBuilder extends GameRoleBuilder
{
    protected function doBuild(): object
    {
        return WerewolfRoleFactory::createOne([
            'role' => $this->roleBuilderBag->getWerewolf()->getEntity(),
        ]);
    }
}
