<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin;

use App\Fixtures\Story\ComplexGame\Runtime\Night\InfectFather\ComplexGameNight3InfectFatherPassedStory;

class ComplexGameWerewolfWinNight3Story extends ComplexGameNight3InfectFatherPassedStory
{
    protected function getWildChildModel(): string
    {
        return self::WEREWOLF_3;
    }

    public function getPrefix(): string
    {
        return 'complex-game-werewolf-win-night-3-';
    }
}
