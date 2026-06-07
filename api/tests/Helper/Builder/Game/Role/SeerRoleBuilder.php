<?php

namespace App\Tests\Helper\Builder\Game\Role;

use App\Entity\Game\Role\SeerRole;
use App\Fixtures\Factory\Game\Role\SeerRoleFactory;

/** @extends GameRoleBuilder<SeerRole> */
class SeerRoleBuilder extends GameRoleBuilder
{
    protected function doBuild(): object
    {
        return SeerRoleFactory::createOne([
            'role' => $this->roleBuilderBag->getSeer()->getEntity(),
        ]);
    }
}
