<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Day;

use App\Fixtures\Story\ComplexGame\Runtime\Night\Witch\ComplexGameNight1WitchSavedStory;
use App\Tests\Helper\Builder\Game\GameBuilder;

class ComplexGameDay1FinishedStory extends ComplexGameNight1WitchSavedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $this->dayOrchestrator->resolve($game);
        $this->voteResolver->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-day-1-finished-';
    }
}
