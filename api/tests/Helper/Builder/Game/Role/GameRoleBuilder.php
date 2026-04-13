<?php

namespace App\Tests\Helper\Builder\Game\Role;

use App\Entity\Game\Role\GameRole;
use App\Tests\Helper\Builder\AbstractBuilder;
use App\Tests\Helper\Builder\Role\RoleBuilderBag;

/**
 * @template T of GameRole
 *
 * @extends AbstractBuilder<T>
 */
abstract class GameRoleBuilder extends AbstractBuilder
{
    public function __construct(
        protected RoleBuilderBag $roleBuilderBag,
    ) {
    }
}
