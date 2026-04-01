<?php

namespace App\Tests\Helper\Builder\Role;

use App\Entity\Game\Role\VillagerRole;
use App\Fixtures\Factory\Game\Role\VillagerRoleFactory;

/** @extends GameRoleBuilder<VillagerRole> */
abstract class VillagerRoleBuilder extends GameRoleBuilder
{
    protected function doBuild(): object
    {
        return VillagerRoleFactory::createOne([
            ...($this->role ? ['role' => $this->role] : []),
        ]);
    }
}
