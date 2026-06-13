<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Night\Seer;

use App\Entity\Game\Role\SeerRole;
use App\Fixtures\Story\ComplexGame\Runtime\Vote\ComplexGameVote1ResolvedStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ComplexGameNight2SeerRevealedStory extends ComplexGameVote1ResolvedStory
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

        $werewolf2PlayerBuilder = $this->getState(self::WEREWOLF_2);
        \assert($werewolf2PlayerBuilder instanceof PlayerBuilder);

        $seerRole->observe($werewolf2PlayerBuilder->getEntity());

        $this->nightOrchestrator->advance($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-night-2-seer-revealed-';
    }
}
