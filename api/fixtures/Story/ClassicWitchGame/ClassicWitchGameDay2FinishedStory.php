<?php

namespace App\Fixtures\Story\ClassicWitchGame;

use App\Tests\Helper\Builder\Game\GameBuilder;

class ClassicWitchGameDay2FinishedStory extends ClassicWitchGameNight2WitchPassedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $this->dayOrchestrator->resolve($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'classic-witch-game-day-2-finished-';
    }
}
