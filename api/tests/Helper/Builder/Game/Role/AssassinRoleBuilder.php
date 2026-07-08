<?php

namespace App\Tests\Helper\Builder\Game\Role;

use App\Entity\Game\Role\AssassinRole;
use App\Fixtures\Factory\Game\Role\AssassinRoleFactory;

/** @extends GameRoleBuilder<AssassinRole> */
class AssassinRoleBuilder extends GameRoleBuilder
{
    protected function doBuild(): object
    {
        return AssassinRoleFactory::createOne([
            'role' => $this->roleBuilderBag->getAssassin()->getEntity(),
        ]);
    }
}
