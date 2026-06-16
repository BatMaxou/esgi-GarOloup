<?php

namespace App\Tests\Helper\Builder\Game\Role;

use App\Entity\Game\Role\HunterRole;
use App\Fixtures\Factory\Game\Role\HunterRoleFactory;

/** @extends GameRoleBuilder<HunterRole> */
class HunterRoleBuilder extends GameRoleBuilder
{
    protected function doBuild(): object
    {
        return HunterRoleFactory::createOne([
            'role' => $this->roleBuilderBag->getHunter()->getEntity(),
        ]);
    }
}
