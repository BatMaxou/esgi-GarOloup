<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Day;

use App\Fixtures\Story\ComplexGame\Runtime\Night\Witch\ComplexGameNight2WitchPassedStory;
use App\Tests\Helper\Builder\Game\GameBuilder;

class ComplexGameDay2FinishedStory extends ComplexGameNight2WitchPassedStory
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
        return 'complex-game-day-2-finished-';
    }
}
