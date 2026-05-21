<?php

namespace App\Fixtures\Story\ClassicGame;

use App\Tests\Helper\Builder\Game\GameBuilder;

class ClassicGameClosedStory extends ClassicGameFilledStory
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
        return 'classic-game-closed-';
    }
}
