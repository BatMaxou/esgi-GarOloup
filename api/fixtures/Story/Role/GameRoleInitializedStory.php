<?php

namespace App\Fixtures\Story\Role;

use App\Fixtures\Story\GaroloupStory;
use App\Tests\Helper\Builder\Game\Role\GameRoleBuilderBag;
use App\Tests\Helper\Builder\Role\RoleBuilderBag;
use App\Tests\Helper\ThereIs;

class GameRoleInitializedStory extends GaroloupStory
{
    public const GAME_ROLE_BAG = 'game_role_bag';

    public function build(): void
    {
        $roleBagBuilder = ThereIs::aStory(RoleInitializedStory::class)
            ->execute()
            ->getState(RoleInitializedStory::ROLE_BAG);
        \assert($roleBagBuilder instanceof RoleBuilderBag);

        $gameRoleBagBuilder = ThereIs::aGameRoleBag($roleBagBuilder);
        $this->addState(self::GAME_ROLE_BAG, $gameRoleBagBuilder);
    }

    public function execute(): void
    {
        $gameRoleBagBuilder = $this->getState(self::GAME_ROLE_BAG);
        \assert($gameRoleBagBuilder instanceof GameRoleBuilderBag);
        $gameRoleBagBuilder->build();
    }
}
