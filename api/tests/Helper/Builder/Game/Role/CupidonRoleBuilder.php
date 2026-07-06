<?php

namespace App\Tests\Helper\Builder\Game\Role;

use App\Entity\Game\Role\CupidonRole;
use App\Fixtures\Factory\Game\Role\CupidonRoleFactory;

/** @extends GameRoleBuilder<CupidonRole> */
class CupidonRoleBuilder extends GameRoleBuilder
{
    protected function doBuild(): object
    {
        return CupidonRoleFactory::createOne([
            'role' => $this->roleBuilderBag->getCupidon()->getEntity(),
        ]);
    }
}
