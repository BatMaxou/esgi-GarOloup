<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Night\Seer;

use App\Entity\Game\Role\SeerRole;
use App\Fixtures\Story\ComplexGame\Runtime\Vote\ComplexGameVote2ResolvedStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ComplexGameNight3SeerRevealedStory extends ComplexGameVote2ResolvedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $seerPlayerBuilder = $this->getState(self::SEER);
        \assert($seerPlayerBuilder instanceof PlayerBuilder);
        $seerRole = $seerPlayerBuilder->getEntity()->getRole();
        \assert($seerRole instanceof SeerRole);

        $werewolf3PlayerBuilder = $this->getState(self::WEREWOLF_3);
        \assert($werewolf3PlayerBuilder instanceof PlayerBuilder);

        $seerRole->observe($werewolf3PlayerBuilder->getEntity());

        $this->nightOrchestrator->advance($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-night-3-seer-revealed-';
    }
}
