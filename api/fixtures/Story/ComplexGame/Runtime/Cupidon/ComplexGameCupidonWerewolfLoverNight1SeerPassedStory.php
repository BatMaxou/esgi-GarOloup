<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Cupidon;

use App\Tests\Helper\Builder\Game\GameBuilder;

class ComplexGameCupidonWerewolfLoverNight1SeerPassedStory extends ComplexGameCupidonWerewolfLoverSetupedStory
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
        return 'complex-game-cupidon-werewolf-lover-night-1-seer-passed-';
    }
}
