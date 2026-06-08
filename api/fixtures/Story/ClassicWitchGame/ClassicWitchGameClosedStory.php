<?php

namespace App\Fixtures\Story\ClassicWitchGame;

use App\Tests\Helper\Builder\Game\GameBuilder;

class ClassicWitchGameClosedStory extends ClassicWitchGameFilledStory
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
        return 'classic-witch-game-closed-';
    }
}
