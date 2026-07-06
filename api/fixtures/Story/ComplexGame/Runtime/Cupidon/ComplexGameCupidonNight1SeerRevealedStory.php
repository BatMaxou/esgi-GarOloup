<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Cupidon;

use App\Entity\Game\Role\SeerRole;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ComplexGameCupidonNight1SeerRevealedStory extends ComplexGameCupidonSetupedStory
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

        $werewolf1PlayerBuilder = $this->getState(self::WEREWOLF_1);
        \assert($werewolf1PlayerBuilder instanceof PlayerBuilder);

        $seerRole->observe($werewolf1PlayerBuilder->getEntity());

        $this->nightOrchestrator->advance($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-cupidon-night-1-seer-revealed-';
    }
}
