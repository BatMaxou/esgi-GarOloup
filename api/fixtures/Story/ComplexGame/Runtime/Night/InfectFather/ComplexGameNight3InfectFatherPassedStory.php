<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Night\InfectFather;

use App\Fixtures\Story\ComplexGame\Runtime\Night\Werewolf\ComplexGameNight3WerewolfVotedStory;
use App\Tests\Helper\Builder\Game\GameBuilder;

class ComplexGameNight3InfectFatherPassedStory extends ComplexGameNight3WerewolfVotedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        $this->nightOrchestrator->advance($gameBuilder->getEntity());

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-night-3-infect-father-passed-';
    }
}
