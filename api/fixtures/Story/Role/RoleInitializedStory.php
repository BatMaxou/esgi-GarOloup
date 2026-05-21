<?php

namespace App\Fixtures\Story\Role;

use App\Fixtures\Story\GaroloupStory;
use App\Tests\Helper\Builder\Role\RoleBuilderBag;
use App\Tests\Helper\ThereIs;

class RoleInitializedStory extends GaroloupStory
{
    public const ROLE_BAG = 'role_bag';
    public const GAME_ROLE_BAG = 'game_role_bag';

    public function build(): void
    {
        $roleBagBuilder = ThereIs::aRoleBag();
        $this->addState(self::ROLE_BAG, $roleBagBuilder);
    }

    public function execute(): void
    {
        $roleBagBuilder = $this->getState(self::ROLE_BAG);
        \assert($roleBagBuilder instanceof RoleBuilderBag);
        $roleBagBuilder->buildAll();
    }
}
