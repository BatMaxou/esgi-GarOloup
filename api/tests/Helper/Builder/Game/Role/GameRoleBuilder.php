<?php

namespace App\Tests\Helper\Builder\Role;

use App\Entity\Game\Role\GameRole;
use App\Entity\Role;
use App\Tests\Helper\Builder\AbstractBuilder;

/**
 * @template T of GameRole
 *
 * @extends AbstractBuilder<T>
 */
abstract class GameRoleBuilder extends AbstractBuilder
{
    public ?Role $role = null;

    public function withRole(Role $role): static
    {
        $this->role = $role;

        return $this;
    }
}
