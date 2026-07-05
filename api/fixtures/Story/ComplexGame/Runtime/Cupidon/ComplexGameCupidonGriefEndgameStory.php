<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Cupidon;

use App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin\ComplexGameWerewolfWinVote5Story;

class ComplexGameCupidonGriefEndgameStory extends ComplexGameWerewolfWinVote5Story
{
    use CupidonPlayerAwareTrait;

    public function build(): void
    {
        parent::build();

        $this->addCupidonPlayer();
    }

    public function execute(): void
    {
        parent::execute();

        $this->setupCouple($this->getFirstLoverState(), $this->getSecondLoverState());

        $this->em->flush();
    }

    public function getFirstLoverState(): string
    {
        return self::WITCH;
    }

    public function getSecondLoverState(): string
    {
        return self::VILLAGER_1;
    }

    public function getPrefix(): string
    {
        return 'complex-game-cupidon-grief-endgame-';
    }
}
