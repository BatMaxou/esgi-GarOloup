<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Assassin;

use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ComplexGameAssassinWinNight4Story extends ComplexGameAssassinWinVote3Story
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $villager2PlayerBuilder = $this->getState(self::VILLAGER_2);
        \assert($villager2PlayerBuilder instanceof PlayerBuilder);
        $this->werewolfNightKill($gameBuilder, $villager2PlayerBuilder);

        $villager3PlayerBuilder = $this->getState(self::VILLAGER_3);
        \assert($villager3PlayerBuilder instanceof PlayerBuilder);
        $this->assassinNightKill($gameBuilder, $villager3PlayerBuilder);

        $this->resolveNight($game);
        $this->dayOrchestrator->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-assassin-win-night-4-';
    }
}
