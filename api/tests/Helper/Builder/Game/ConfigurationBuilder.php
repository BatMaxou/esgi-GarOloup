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
    public bool $withGameMaster = false;
    public bool $withRandomDispatch = true;

    protected function doBuild(): object
    {
        return ConfigurationFactory::createOne([
            ...($this->game ? ['game' => $this->game->getEntity()] : []),
            ...($this->composition ? ['composition' => $this->composition->getEntity()] : []),
            'withGameMaster' => $this->withGameMaster,
            'withRandomDispatch' => $this->withRandomDispatch,
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

    public function withGameMaster(): static
    {
        $this->withGameMaster = true;

        return $this;
    }

    public function withoutRandomDispatch(): static
    {
        $this->withRandomDispatch = false;

        return $this;
    }
}
