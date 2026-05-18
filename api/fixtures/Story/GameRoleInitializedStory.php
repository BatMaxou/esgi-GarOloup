<?php

namespace App\Fixtures\Story;

use App\Tests\Helper\Builder\Role\RoleBuilderBag;
use App\Tests\Helper\ThereIs;
use Zenstruck\Foundry\Story;

final class GameRoleInitializedStory extends Story
{
    public const GAME_ROLE_BAG = 'game_role_bag';

    public function build(): void
    {
        $roleBagBuilder = RoleInitializedStory::load()->getState(RoleInitializedStory::ROLE_BAG);
        \assert($roleBagBuilder instanceof RoleBuilderBag);

        $gameRoleBagBuilder = ThereIs::aGameRoleBag($roleBagBuilder)->build();

        $this->addState(self::GAME_ROLE_BAG, $gameRoleBagBuilder);
    }
}
