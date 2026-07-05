<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Cupidon;

class ComplexGameCupidonWerewolfLoverSetupedStory extends ComplexGameCupidonSetupedStory
{
    public function getSecondLoverState(): string
    {
        return self::WEREWOLF_1;
    }

    public function getPrefix(): string
    {
        return 'complex-game-cupidon-werewolf-lover-setuped-';
    }
}
