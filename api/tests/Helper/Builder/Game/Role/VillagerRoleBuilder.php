<?php

namespace App\Tests\Helper\Builder\Game\Role;

use App\Entity\Game\Role\VillagerRole;
use App\Fixtures\Factory\Game\Role\VillagerRoleFactory;

/** @extends GameRoleBuilder<VillagerRole> */
class VillagerRoleBuilder extends GameRoleBuilder
{
    protected function doBuild(): object
    {
        return VillagerRoleFactory::createOne([
            'role' => $this->roleBuilderBag->getVillager()->getEntity(),
        ]);
    }
}
