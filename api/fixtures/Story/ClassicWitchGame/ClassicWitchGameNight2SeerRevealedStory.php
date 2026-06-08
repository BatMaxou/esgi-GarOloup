<?php

namespace App\Fixtures\Story\ClassicWitchGame;

use App\Entity\Game\Role\SeerRole;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ClassicWitchGameNight2SeerRevealedStory extends ClassicWitchGameVote1ResolvedStory
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
        return 'classic-witch-game-night-2-seer-revealed-';
    }
}
