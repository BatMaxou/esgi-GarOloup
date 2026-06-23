<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin;

use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ComplexGameWerewolfWinNight5Story extends ComplexGameWerewolfWinVote4Story
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $this->nightOrchestrator->advance($game);

        $victimPlayerBuilder = $this->getState(self::SEER);
        \assert($victimPlayerBuilder instanceof PlayerBuilder);
        $this->werewolfKill($gameBuilder, $victimPlayerBuilder);
        $this->nightOrchestrator->advance($game);

        $this->nightOrchestrator->advance($game);

        $this->nightOrchestrator->advance($game);
        $this->dayOrchestrator->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-werewolf-win-night-5-';
    }
}
