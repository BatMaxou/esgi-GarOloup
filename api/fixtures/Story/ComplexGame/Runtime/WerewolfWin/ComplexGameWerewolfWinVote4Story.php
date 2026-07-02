<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin;

use App\Entity\Game\Role\HunterRole;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ComplexGameWerewolfWinVote4Story extends ComplexGameWerewolfWinNight4Story
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $this->dayOrchestrator->resolve($game);
        $this->voteResolver->start($game);

        $targetPlayerBuilder = $this->getState(self::HUNTER);
        \assert($targetPlayerBuilder instanceof PlayerBuilder);
        $this->villageMisvote($gameBuilder, $targetPlayerBuilder);

        $this->voteResolver->resolve($game);

        $hunterRole = $targetPlayerBuilder->getEntity()->getRole();
        \assert($hunterRole instanceof HunterRole);
        $hunterRole->markShot();

        $this->nightOrchestrator->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-werewolf-win-vote-4-resolved-';
    }
}
