<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Cupidon;

use App\Fixtures\Story\ComplexGame\Runtime\Setup\ComplexGameSetupedStory;

class ComplexGameCupidonSetupedStory extends ComplexGameSetupedStory
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
        return self::VILLAGER_2;
    }

    public function getSecondLoverState(): string
    {
        return self::VILLAGER_3;
    }

    public function getPrefix(): string
    {
        return 'complex-game-cupidon-setuped-';
    }
}
