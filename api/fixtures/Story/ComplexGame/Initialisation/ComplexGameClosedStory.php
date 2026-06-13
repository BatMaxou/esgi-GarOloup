<?php

namespace App\Fixtures\Story\ComplexGame\Initialisation;

use App\Tests\Helper\Builder\Game\GameBuilder;

class ComplexGameClosedStory extends ComplexGameFilledStory
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
        return 'complex-game-closed-';
    }
}
