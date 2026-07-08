<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Assassin;

use App\Entity\Game\Role\HunterRole;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ComplexGameAssassinWinVote3Story extends ComplexGameAssassinWinNight3Story
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $this->dayOrchestrator->resolve($game);
        $this->voteResolver->start($game);

        $hunterPlayerBuilder = $this->getState(self::HUNTER);
        \assert($hunterPlayerBuilder instanceof PlayerBuilder);
        $this->villageVoteOut($gameBuilder, $hunterPlayerBuilder);

        $this->voteResolver->resolve($game);

        $hunterRole = $hunterPlayerBuilder->getEntity()->getRole();
        \assert($hunterRole instanceof HunterRole);
        $hunterRole->markShot();

        $this->nightOrchestrator->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-assassin-win-vote-3-';
    }
}
