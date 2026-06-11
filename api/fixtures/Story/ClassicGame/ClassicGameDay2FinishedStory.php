<?php

namespace App\Fixtures\Story\ClassicGame;

use App\Tests\Helper\Builder\Game\GameBuilder;

class ClassicGameDay2FinishedStory extends ClassicGameNight2WerewolfVotedStory
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
        return 'classic-game-day-2-finished-';
    }
}
