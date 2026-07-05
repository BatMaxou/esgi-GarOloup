<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Cupidon;

use App\Tests\Helper\Builder\Game\GameBuilder;

class ComplexGameCupidonNight1WitchPassedStory extends ComplexGameCupidonNight1InfectFatherPassedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $this->nightOrchestrator->advance($game);
        $this->dayOrchestrator->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-cupidon-night-1-witch-passed-';
    }
}
