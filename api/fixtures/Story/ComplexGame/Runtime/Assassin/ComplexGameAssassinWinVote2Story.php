<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Assassin;

use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ComplexGameAssassinWinVote2Story extends ComplexGameAssassinWinNight2Story
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $this->dayOrchestrator->resolve($game);
        $this->voteResolver->start($game);

        $werewolf2PlayerBuilder = $this->getState(self::WEREWOLF_2);
        \assert($werewolf2PlayerBuilder instanceof PlayerBuilder);
        $this->villageVoteOut($gameBuilder, $werewolf2PlayerBuilder);

        $this->voteResolver->resolve($game);
        $this->nightOrchestrator->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-assassin-win-vote-2-';
    }
}
