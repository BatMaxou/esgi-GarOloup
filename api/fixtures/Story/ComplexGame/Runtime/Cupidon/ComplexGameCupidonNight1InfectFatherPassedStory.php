<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Cupidon;

use App\Tests\Helper\Builder\Game\GameBuilder;

class ComplexGameCupidonNight1InfectFatherPassedStory extends ComplexGameCupidonNight1LoverKilledStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        $this->nightOrchestrator->advance($gameBuilder->getEntity());

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-cupidon-night-1-infect-father-passed-';
    }
}
