<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Vote;

use App\Fixtures\Story\ComplexGame\Runtime\Day\ComplexGameDay1FinishedStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

class ComplexGameVote1HunterEliminatedStory extends ComplexGameDay1FinishedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $hunterPlayerBuilder = $this->getState(self::HUNTER);
        \assert($hunterPlayerBuilder instanceof PlayerBuilder);

        foreach ($this->getPool(self::VILLAGERS_POOL) as $villagerPlayerBuilder) {
            \assert($villagerPlayerBuilder instanceof PlayerBuilder);
            if ($villagerPlayerBuilder->getEntity()->isDead()) {
                continue;
            }

            ThereIs::aBallot()
                ->forGame($gameBuilder)
                ->by($villagerPlayerBuilder)
                ->against($hunterPlayerBuilder)
                ->build();
        }

        $this->voteResolver->resolve($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-vote-1-hunter-eliminated-';
    }
}
