<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Assassin;

use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ComplexGameAssassinWinNight3Story extends ComplexGameAssassinWinVote2Story
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $seerPlayerBuilder = $this->getState(self::SEER);
        \assert($seerPlayerBuilder instanceof PlayerBuilder);
        $this->werewolfNightKill($gameBuilder, $seerPlayerBuilder);

        $witchPlayerBuilder = $this->getState(self::WITCH);
        \assert($witchPlayerBuilder instanceof PlayerBuilder);
        $this->assassinNightKill($gameBuilder, $witchPlayerBuilder);

        $this->resolveNight($game);
        $this->dayOrchestrator->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-assassin-win-night-3-';
    }
}
