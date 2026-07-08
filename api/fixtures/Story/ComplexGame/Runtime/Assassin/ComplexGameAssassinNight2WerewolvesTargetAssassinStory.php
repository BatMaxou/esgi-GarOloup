<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Assassin;

class ComplexGameAssassinNight2WerewolvesTargetAssassinStory extends ComplexGameAssassinNight2Story
{
    protected function getWerewolfTarget(): string
    {
        return self::ASSASSIN;
    }

    public function getPrefix(): string
    {
        return 'complex-game-assassin-night-2-werewolves-target-assassin-';
    }
}
