<?php

namespace App\Tests\Helper\Builder\Game\Role;

use App\Entity\Game\Role\WildChildRole;
use App\Fixtures\Factory\Game\Role\WildChildRoleFactory;

/** @extends GameRoleBuilder<WildChildRole> */
class WildChildRoleBuilder extends GameRoleBuilder
{
    protected function doBuild(): object
    {
        return WildChildRoleFactory::createOne([
            'role' => $this->roleBuilderBag->getWildChild()->getEntity(),
        ]);
    }
}
