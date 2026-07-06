<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Cupidon;

use App\Fixtures\Story\ComplexGame\Initialisation\ComplexGameLaunchedStory;

class ComplexGameCupidonLaunchedStory extends ComplexGameLaunchedStory
{
    use CupidonPlayerAwareTrait;

    public function build(): void
    {
        parent::build();

        $this->addCupidonPlayer();
    }

    public function getPrefix(): string
    {
        return 'complex-game-cupidon-launched-';
    }
}
