<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Assassin;

use App\Fixtures\Story\ComplexGame\Runtime\Setup\ComplexGameSetupedStory;

class ComplexGameAssassinSetupedStory extends ComplexGameSetupedStory
{
    use AssassinAwareTrait;

    public function build(): void
    {
        parent::build();

        $this->addAssassinToComposition();
        $this->addAssassinPlayer();
    }

    public function getPrefix(): string
    {
        return 'complex-game-assassin-setuped-';
    }
}
