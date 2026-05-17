<?php

namespace App\Tests\Helper\Builder\Game\Role;

use App\Entity\Game\Role\VillagerRole;
use App\Fixtures\Factory\Game\Role\VillagerRoleFactory;

/** @extends GameRoleBuilder<VillagerRole> */
class VillagerRoleBuilder extends GameRoleBuilder
{
    public ?string $friendId = null;

    protected function doBuild(): object
    {
        return VillagerRoleFactory::createOne([
            ...($this->friendId ? ['friendId' => $this->friendId] : []),
            'role' => $this->roleBuilderBag->getVillager()->getEntity(),
        ]);
    }

    public function withFriendId(string $friendId): static
    {
        $this->friendId = $friendId;

        return $this;
    }
}
