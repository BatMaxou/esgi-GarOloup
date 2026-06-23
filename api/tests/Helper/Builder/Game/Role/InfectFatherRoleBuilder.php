<?php

namespace App\Tests\Helper\Builder\Game\Role;

use App\Entity\Game\Role\InfectFatherRole;
use App\Fixtures\Factory\Game\Role\InfectFatherRoleFactory;

/** @extends GameRoleBuilder<InfectFatherRole> */
class InfectFatherRoleBuilder extends GameRoleBuilder
{
    protected function doBuild(): object
    {
        return InfectFatherRoleFactory::createOne([
            'role' => $this->roleBuilderBag->getInfectFather()->getEntity(),
        ]);
    }
}
