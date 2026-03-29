<?php

namespace App\Tests\Helper\Builder\Game;

use App\Entity\Game\Configuration;
use App\Fixtures\Factory\Game\ConfigurationFactory;
use App\Tests\Helper\Builder\AbstractBuilder;

/** @extends AbstractBuilder<Configuration> */
class ConfigurationBuilder extends AbstractBuilder
{
    public ?GameBuilder $game = null;
    public ?CompositionBuilder $composition = null;

    protected function doBuild(): object
    {
        return ConfigurationFactory::createOne([
            ...($this->game ? ['game' => $this->game->getEntity()] : []),
            ...($this->composition ? ['composition' => $this->composition->getEntity()] : []),
        ]);
    }

    public function forGame(GameBuilder $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function withComposition(CompositionBuilder $composition): static
    {
        $this->composition = $composition;

        return $this;
    }
}
