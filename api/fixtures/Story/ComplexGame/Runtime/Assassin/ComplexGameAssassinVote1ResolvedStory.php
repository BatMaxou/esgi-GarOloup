<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Assassin;

use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

class ComplexGameAssassinVote1ResolvedStory extends ComplexGameAssassinDay1FinishedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $werewolfPlayerBuilder = $this->getState(self::WEREWOLF_1);
        \assert($werewolfPlayerBuilder instanceof PlayerBuilder);

        foreach ($this->getPool(self::VILLAGERS_POOL) as $villagerPlayerBuilder) {
            \assert($villagerPlayerBuilder instanceof PlayerBuilder);
            if ($villagerPlayerBuilder->getEntity()->isDead()) {
                continue;
            }

            ThereIs::aBallot()
                ->forGame($gameBuilder)
                ->by($villagerPlayerBuilder)
                ->against($werewolfPlayerBuilder)
                ->build();
        }

        $this->voteResolver->resolve($game);
        $this->nightOrchestrator->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-assassin-vote-1-resolved-';
    }
}
