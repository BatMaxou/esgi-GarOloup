<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\HunterEndgame;

use App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin\ComplexGameWerewolfWinNight4Story;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ComplexGameHunterEndgameVoteStory extends ComplexGameWerewolfWinNight4Story
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $this->dayOrchestrator->resolve($game);
        $this->voteResolver->start($game);

        $seerPlayerBuilder = $this->getState(self::SEER);
        \assert($seerPlayerBuilder instanceof PlayerBuilder);
        $this->villageMisvote($gameBuilder, $seerPlayerBuilder);

        $this->voteResolver->resolve($game);
        $this->nightOrchestrator->start($game);

        $witchPlayerBuilder = $this->getState(self::WITCH);
        \assert($witchPlayerBuilder instanceof PlayerBuilder);
        $this->werewolfKill($gameBuilder, $witchPlayerBuilder);

        $this->nightOrchestrator->advance($game);
        $this->dayOrchestrator->start($game);

        $this->dayOrchestrator->resolve($game);
        $this->voteResolver->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-hunter-endgame-vote-';
    }
}
