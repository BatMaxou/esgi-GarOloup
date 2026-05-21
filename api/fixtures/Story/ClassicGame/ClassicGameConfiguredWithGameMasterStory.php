<?php

namespace App\Fixtures\Story\ClassicGame;

use App\Enum\Game\GameInitialisationStepEnum;
use App\Tests\Helper\Builder\Game\ConfigurationBuilder;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Role\RoleBuilderBag;

class ClassicGameConfiguredWithGameMasterStory extends ClassicGameConfiguredStory
{
    public const GAME_MASTER = 'game_master';

    public function build(): void
    {
        parent::build();

        $roleBagBuilder = $this->getState(self::ROLE_BAG);
        \assert($roleBagBuilder instanceof RoleBuilderBag);

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        $configurationBuilder = $this->getState(self::CONFIGURATION);
        \assert($configurationBuilder instanceof ConfigurationBuilder);

        $configurationBuilder->withGameMaster();
        $gameBuilder->withInitialisationStep(GameInitialisationStepEnum::GAME_MASTER_CHOICE);
    }

    public function getPrefix(): string
    {
        return 'classic-game-configured-with-game-master-';
    }
}
