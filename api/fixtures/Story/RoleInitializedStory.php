<?php

namespace App\Fixtures\Story;

use App\Tests\Helper\ThereIs;
use Zenstruck\Foundry\Story;

final class RoleInitializedStory extends Story
{
    public const ROLE_BAG = 'role_bag';

    public function build(): void
    {
        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();

        $this->addState(self::ROLE_BAG, $roleBagBuilder);
    }
}
