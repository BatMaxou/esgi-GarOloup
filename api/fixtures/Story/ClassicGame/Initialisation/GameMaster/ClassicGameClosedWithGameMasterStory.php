<?php

namespace App\Fixtures\Story\ClassicGame\Initialisation\GameMaster;

use App\Tests\Helper\Builder\Game\GameBuilder;

class ClassicGameClosedWithGameMasterStory extends ClassicGameFilledWithGameMasterStory
{
    public function build(): void
    {
        parent::build();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $gameBuilder->closed();
    }

    public function getPrefix(): string
    {
        return 'classic-game-closed-with-game-master-';
    }
}
