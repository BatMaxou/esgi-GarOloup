<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Assassin;

use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

class ComplexGameAssassinDayVoteKillsAssassinStory extends ComplexGameAssassinDay1FinishedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $assassinPlayerBuilder = $this->getState(self::ASSASSIN);
        \assert($assassinPlayerBuilder instanceof PlayerBuilder);

        foreach ($this->getPool(self::VILLAGERS_POOL) as $villagerPlayerBuilder) {
            \assert($villagerPlayerBuilder instanceof PlayerBuilder);
            if ($villagerPlayerBuilder->getEntity()->isDead()) {
                continue;
            }

            ThereIs::aBallot()
                ->forGame($gameBuilder)
                ->by($villagerPlayerBuilder)
                ->against($assassinPlayerBuilder)
                ->build();
        }

        $this->voteResolver->resolve($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-assassin-day-vote-kills-assassin-';
    }
}
