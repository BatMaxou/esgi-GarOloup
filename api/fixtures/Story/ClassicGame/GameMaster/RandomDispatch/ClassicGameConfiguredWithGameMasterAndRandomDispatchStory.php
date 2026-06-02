<?php

namespace App\Fixtures\Story\ClassicGame\GameMaster\RandomDispatch;

use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameConfiguredWithGameMasterStory;
use App\Tests\Helper\Builder\Game\ConfigurationBuilder;

class ClassicGameConfiguredWithGameMasterAndRandomDispatchStory extends ClassicGameConfiguredWithGameMasterStory
{
    public function build(): void
    {
        parent::build();

        $configurationBuilder = $this->getState(self::CONFIGURATION);
        \assert($configurationBuilder instanceof ConfigurationBuilder);

        $configurationBuilder->withRandomDispatch();
    }

    public function getPrefix(): string
    {
        return 'classic-game-configured-with-game-master-and-random-dispatch-';
    }
}
