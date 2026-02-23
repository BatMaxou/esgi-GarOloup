<?php

namespace App\Tests\Helper\Builder\Player;

use App\Entity\Game;
use App\Enum\GameStepEnum;
use App\Fixtures\Factory\GameFactory;
use App\Tests\Helper\Builder\AbstractBuilder;

/** @extends AbstractBuilder<Game> */
class GameBuilder extends AbstractBuilder
{
    public ?PlayerBuilder $host = null;
    public ?string $joinCode = null;
    public ?GameStepEnum $step = null;

    protected function doBuild(): object
    {
        return GameFactory::createOne([
            ...($this->host ? ['host' => $this->host->getEntity()] : []),
        ]);
    }

    public function withHost(PlayerBuilder $host): static
    {
        $this->host = $host;

        return $this;
    }

    public function withJoinCode(string $joinCode): static
    {
        $this->joinCode = $joinCode;

        return $this;
    }

    public function withStep(GameStepEnum $step): static
    {
        $this->step = $step;

        return $this;
    }
}
