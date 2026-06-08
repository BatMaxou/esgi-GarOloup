<?php

namespace App\Tests\Helper\Builder\Game\Role;

use App\Entity\Game\Role\WitchRole;
use App\Fixtures\Factory\Game\Role\WitchRoleFactory;

/** @extends GameRoleBuilder<WitchRole> */
class WitchRoleBuilder extends GameRoleBuilder
{
    protected function doBuild(): object
    {
        return WitchRoleFactory::createOne([
            'role' => $this->roleBuilderBag->getWitch()->getEntity(),
        ]);
    }
}
