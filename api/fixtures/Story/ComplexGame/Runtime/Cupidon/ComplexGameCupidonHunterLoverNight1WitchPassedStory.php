<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Cupidon;

class ComplexGameCupidonHunterLoverNight1WitchPassedStory extends ComplexGameCupidonNight1WitchPassedStory
{
    public function getSecondLoverState(): string
    {
        return self::HUNTER;
    }

    public function getPrefix(): string
    {
        return 'complex-game-cupidon-hunter-lover-night-1-witch-passed-';
    }
}
