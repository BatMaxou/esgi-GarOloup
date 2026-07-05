<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Cupidon;

use App\Entity\Game\Role\WitchRole;
use App\Enum\Game\GameRoleEnum;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

class ComplexGameCupidonNight1BothLoversKilledStory extends ComplexGameCupidonNight1InfectFatherPassedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $witchPlayerBuilder = $this->getState(self::WITCH);
        \assert($witchPlayerBuilder instanceof PlayerBuilder);
        $witchRole = $witchPlayerBuilder->getEntity()->getRole();
        \assert($witchRole instanceof WitchRole);

        $victimPlayerBuilder = $this->getState($this->getSecondLoverState());
        \assert($victimPlayerBuilder instanceof PlayerBuilder);

        ThereIs::aMurderAction()
            ->forGame($gameBuilder)
            ->from(GameRoleEnum::WITCH)
            ->against($victimPlayerBuilder)
            ->build();

        $witchRole->usePoisonPotion();

        $this->nightOrchestrator->advance($game);
        $this->dayOrchestrator->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-cupidon-night-1-both-lovers-killed-';
    }
}
