<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Assassin;

use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ComplexGameAssassinWinNight2Story extends ComplexGameAssassinVote1ResolvedStory
{
    use AssassinEndgameTrait;

    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $wildChildPlayerBuilder = $this->getState(self::WILD_CHILD);
        \assert($wildChildPlayerBuilder instanceof PlayerBuilder);
        $this->werewolfNightKill($gameBuilder, $wildChildPlayerBuilder);

        $villager5PlayerBuilder = $this->getState(self::VILLAGER_5);
        \assert($villager5PlayerBuilder instanceof PlayerBuilder);
        $this->assassinNightKill($gameBuilder, $villager5PlayerBuilder);

        $this->resolveNight($game);
        $this->dayOrchestrator->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-assassin-win-night-2-';
    }
}
