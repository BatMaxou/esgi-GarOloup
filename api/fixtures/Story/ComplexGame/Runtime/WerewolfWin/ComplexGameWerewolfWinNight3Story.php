<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin;

use App\Fixtures\Story\ComplexGame\Runtime\Night\Werewolf\ComplexGameNight3WerewolfVotedStory;

class ComplexGameWerewolfWinNight3Story extends ComplexGameNight3WerewolfVotedStory
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
