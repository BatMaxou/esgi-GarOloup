<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Assassin;

use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ComplexGameAssassinWinVote4Story extends ComplexGameAssassinWinNight4Story
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $this->dayOrchestrator->resolve($game);
        $this->voteResolver->start($game);

        $werewolf3PlayerBuilder = $this->getState(self::WEREWOLF_3);
        \assert($werewolf3PlayerBuilder instanceof PlayerBuilder);
        $this->villageVoteOut($gameBuilder, $werewolf3PlayerBuilder);

        $this->voteResolver->resolve($game);
        $this->nightOrchestrator->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-assassin-win-vote-4-';
    }
}
