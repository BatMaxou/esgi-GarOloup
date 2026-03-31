<?php

namespace App\Tests\Helper\Builder\Game;

use App\Api\Model\Game\Composition\CompositionInput;
use App\Api\Model\Game\Composition\RoleEntryInput;
use App\Entity\Game\Composition;
use App\Entity\Role;
use App\Fixtures\Factory\Game\CompositionFactory;
use App\Fixtures\Factory\Game\RoleEntryFactory;
use App\Tests\Helper\Builder\AbstractBuilder;
use App\Tests\Helper\Builder\Role\RoleBuilder;

/** @extends AbstractBuilder<Composition> */
class CompositionBuilder extends AbstractBuilder
{
    /** @var array<array{role: RoleBuilder, count: int}> */
    public array $roles = [];

    protected function doBuild(): object
    {
        $composition = CompositionFactory::createOne();

        foreach ($this->roles as ['role' => $role, 'count' => $count]) {
            RoleEntryFactory::createOne([
                'composition' => $composition,
                'role' => $role->getEntity(),
                'count' => $count,
            ]);
        }

        return $composition;
    }

    public function withRole(RoleBuilder $role, int $count = 1): static
    {
        $this->roles[] = ['role' => $role, 'count' => $count];

        return $this;
    }

    public function toInput(): CompositionInput
    {
        $roles = [];
        foreach ($this->roles as $role) {
            $type = $role['role']->getEntity()->getType();
            if (!$type) {
                continue;
            }

            $roles[] = new RoleEntryInput($type, $role['count']);
        }

        return new CompositionInput($roles);
    }
}
