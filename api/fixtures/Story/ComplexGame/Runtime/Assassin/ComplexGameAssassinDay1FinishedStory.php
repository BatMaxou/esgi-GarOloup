<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Assassin;

use App\Tests\Helper\Builder\Game\GameBuilder;

class ComplexGameAssassinDay1FinishedStory extends ComplexGameAssassinNight1ResolvedStory
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
        return 'complex-game-assassin-day-1-finished-';
    }
}
